<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\InventoryItem;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Data untuk hari ini
        $today = Carbon::today();
        
        $todaySales = Order::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->sum('total_amount');
            
        $todayOrders = Order::whereDate('created_at', $today)->count();
        
        $pendingOrders = Order::where('status', 'pending')->count();
        
        // Produk terlaris
        $topProducts = Product::withCount(['orderItems as sales_count' => function($query) {
                $query->whereHas('order', function($q) {
                    $q->where('status', 'completed');
                });
            }])
            ->orderBy('sales_count', 'desc')
            ->take(5)
            ->get();
            
        // Item stok rendah
        $lowStockItems = InventoryItem::whereRaw('stock_quantity <= alert_threshold')->get();
        
        // Statistik bulanan
        $monthlySales = Order::where('status', 'completed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('total_amount');
        
        return view('dashboard', compact(
            'todaySales',
            'todayOrders',
            'pendingOrders',
            'topProducts',
            'lowStockItems',
            'monthlySales'
        ));
    }
}
