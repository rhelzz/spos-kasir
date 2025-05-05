<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:cashier');
    }
    
    public function index()
    {
        // Get today's date
        $today = Carbon::today();
        
        // Get orders handled by current cashier today
        $myOrdersToday = Order::where('user_id', Auth::id())
            ->whereDate('created_at', $today)
            ->count();
            
        $myRevenueToday = Order::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->whereDate('created_at', $today)
            ->sum('total_amount');
            
        // Get pending and completed orders
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'completed')
            ->whereDate('created_at', $today)
            ->count();
            
        // Get tables with status
        $tables = Table::orderBy('name')->get();
        
        // Get popular products (top selling)
        $popularProducts = Product::select(
                'products.*',
                DB::raw('SUM(order_items.quantity) as total_quantity')
            )
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('products.is_active', true)
            ->where('products.stock', '>', 0)
            ->where(function($query) {
                $query->whereNull('orders.id')
                    ->orWhere('orders.status', '=', 'completed');
            })
            ->groupBy(
                'products.id', 
                'products.name', 
                'products.description', 
                'products.price', 
                'products.cost_price',
                'products.stock',
                'products.min_stock',
                'products.category_id',
                'products.is_active',
                'products.created_at',
                'products.updated_at'
            )
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();
            
        // Get recent orders
        $recentOrders = Order::with(['user', 'table'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        return view('cashier.dashboard', compact(
            'myOrdersToday',
            'myRevenueToday',
            'pendingOrders',
            'completedOrders',
            'tables',
            'popularProducts',
            'recentOrders'
        ));
    }
}
