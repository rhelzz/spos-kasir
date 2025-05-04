<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Routing\Controller;
use Carbon\Carbon;

class ShiftController extends Controller
{
    public function index(): View
    {
        // Ambil shift untuk user saat ini
        $shifts = Shift::where('user_id', auth()->id())
                      ->orderBy('created_at', 'desc')
                      ->paginate(10);
                      
        // Cek apakah user memiliki shift yang sedang aktif
        $activeShift = Shift::where('user_id', auth()->id())
                           ->whereNull('end_time')
                           ->first();
                           
        return view('shifts.index', compact('shifts', 'activeShift'));
    }
    
    public function openShift(Request $request): RedirectResponse
    {
        // Cek apakah user sudah memiliki shift aktif
        $activeShift = Shift::where('user_id', auth()->id())
                          ->whereNull('end_time')
                          ->first();
                          
        if ($activeShift) {
            return back()->with('error', 'Anda sudah memiliki shift yang aktif.');
        }
        
        $validated = $request->validate([
            'opening_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        
        $shift = Shift::create([
            'user_id' => auth()->id(),
            'start_time' => Carbon::now(),
            'opening_cash' => $validated['opening_cash'],
            'notes' => $validated['notes'] ?? null,
        ]);
        
        return redirect()->route('shifts.show', $shift)->with('success', 'Shift berhasil dibuka.');
    }
    
    public function closeShift(Request $request): RedirectResponse
    {
        // Cek apakah user memiliki shift aktif
        $activeShift = Shift::where('user_id', auth()->id())
                          ->whereNull('end_time')
                          ->first();
                          
        if (!$activeShift) {
            return back()->with('error', 'Anda tidak memiliki shift aktif untuk ditutup.');
        }
        
        $validated = $request->validate([
            'closing_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        
        // Hitung total penjualan selama shift
        $shiftSales = Payment::whereHas('order', function($query) use ($activeShift) {
                        $query->where('user_id', auth()->id())
                              ->where('status', 'completed')
                              ->where('created_at', '>=', $activeShift->start_time);
                    })
                    ->sum('amount_paid');
                    
        // Hitung jumlah pesanan selama shift
        $orderCount = Order::where('user_id', auth()->id())
                          ->where('created_at', '>=', $activeShift->start_time)
                          ->count();
                          
        // Hitung selisih kas
        $expectedCash = $activeShift->opening_cash + $shiftSales;
        $cashDifference = $validated['closing_cash'] - $expectedCash;
        
        $activeShift->update([
            'end_time' => Carbon::now(),
            'closing_cash' => $validated['closing_cash'],
            'sales_total' => $shiftSales,
            'order_count' => $orderCount,
            'cash_difference' => $cashDifference,
            'notes' => $activeShift->notes . "\n\n" . ($validated['notes'] ?? ''),
        ]);
        
        return redirect()->route('shifts.show', $activeShift)->with('success', 'Shift berhasil ditutup.');
    }
    
    public function show(Shift $shift): View
    {
        // Ambil pesanan untuk shift ini
        $orders = Order::where('user_id', $shift->user_id)
                      ->whereBetween('created_at', [
                          $shift->start_time, 
                          $shift->end_time ?? Carbon::now()
                      ])
                      ->with('payment')
                      ->get();
        
        // Kelompokkan pembayaran berdasarkan metode
        $paymentsByMethod = $orders->groupBy('payment.payment_method')
                                 ->map(function($group) {
                                     return [
                                         'count' => $group->count(),
                                         'total' => $group->sum('total_amount')
                                     ];
                                 });
        
        return view('shifts.show', compact('shift', 'orders', 'paymentsByMethod'));
    }
}
