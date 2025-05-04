<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\InventoryItem;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    public function sales(Request $request): View
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();
        
        $orders = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->with('user', 'payment')
            ->get();
            
        $totalSales = $orders->sum('total_amount');
        $averageSale = $orders->count() > 0 ? $totalSales / $orders->count() : 0;
        
        $salesByPaymentMethod = $orders->groupBy('payment.payment_method')
            ->map(function ($groupOrders) {
                return [
                    'count' => $groupOrders->count(),
                    'total' => $groupOrders->sum('total_amount')
                ];
            });
            
        return view('reports.sales', compact('orders', 'totalSales', 'averageSale', 'salesByPaymentMethod', 'startDate', 'endDate'));
    }
    
    public function inventory(): View
    {
        $inventoryItems = InventoryItem::all();
        
        $lowStockItems = $inventoryItems->filter(function($item) {
            return $item->stock_quantity <= $item->alert_threshold;
        });
        
        $totalInventoryValue = $inventoryItems->sum(function($item) {
            return $item->stock_quantity * $item->cost_per_unit;
        });
        
        return view('reports.inventory', compact('inventoryItems', 'lowStockItems', 'totalInventoryValue'));
    }
    
    public function products(): View
    {
        $products = Product::withCount(['orderItems as sales_count' => function($query) {
                $query->whereHas('order', function($q) {
                    $q->where('status', 'completed');
                });
            }])
            ->with('category')
            ->get();
            
        $productsByCategory = $products->groupBy('category.name');
        
        return view('reports.products', compact('products', 'productsByCategory'));
    }
    
    public function export(Request $request, string $type)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();
        
        switch ($type) {
            case 'sales':
                $data = Order::where('status', 'completed')
                    ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                    ->with('user', 'payment')
                    ->get()
                    ->toArray();
                break;
                
            case 'inventory':
                $data = InventoryItem::all()->toArray();
                break;
                
            case 'products':
                $data = Product::withCount(['orderItems as sales_count' => function($query) {
                        $query->whereHas('order', function($q) {
                            $q->where('status', 'completed');
                        });
                    }])
                    ->with('category')
                    ->get()
                    ->toArray();
                break;
                
            default:
                return back()->with('error', 'Tipe laporan tidak valid.');
        }
        
        // Persiapkan data untuk CSV
        $filename = $type . '_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv';
        
        // Logika export sederhana
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        
        $callback = function() use ($data, $type) {
            $file = fopen('php://output', 'w');
            
            // Tulis header sesuai tipe laporan
            switch ($type) {
                case 'sales':
                    fputcsv($file, ['ID Pesanan', 'Nomor Pesanan', 'Tanggal', 'Total', 'Metode Pembayaran', 'Kasir']);
                    foreach ($data as $row) {
                        fputcsv($file, [
                            $row['id'],
                            $row['order_number'],
                            $row['created_at'],
                            $row['total_amount'],
                            $row['payment']['payment_method'] ?? 'N/A',
                            $row['user']['name'] ?? 'N/A'
                        ]);
                    }
                    break;
                    
                case 'inventory':
                    fputcsv($file, ['ID', 'Nama Item', 'Unit', 'Stok', 'Harga/Unit', 'Nilai Total']);
                    foreach ($data as $row) {
                        fputcsv($file, [
                            $row['id'],
                            $row['name'],
                            $row['unit'],
                            $row['stock_quantity'],
                            $row['cost_per_unit'],
                            $row['stock_quantity'] * $row['cost_per_unit']
                        ]);
                    }
                    break;
                    
                case 'products':
                    fputcsv($file, ['ID', 'Nama Produk', 'Kategori', 'Harga', 'Jumlah Terjual']);
                    foreach ($data as $row) {
                        fputcsv($file, [
                            $row['id'],
                            $row['name'],
                            $row['category']['name'] ?? 'N/A',
                            $row['price'],
                            $row['sales_count']
                        ]);
                    }
                    break;
            }
            
            fclose($file);
        };
        
        return Response::stream($callback, 200, $headers);
    }
}
