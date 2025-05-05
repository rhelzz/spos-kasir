<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:owner');
    }
    
    public function index()
    {
        // Get today's date and current month/year
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        // Summary statistics
        $totalSales = Order::where('status', 'completed')->sum('total_amount');
        $monthlySales = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('total_amount');
        $dailySales = Order::where('status', 'completed')
            ->whereDate('created_at', $today)
            ->sum('total_amount');
            
        $totalProducts = Product::count();
        $totalUsers = User::count();
        
        // Low stock products
        $lowStockProducts = Product::whereRaw('stock <= min_stock')
            ->where('stock', '>', 0)
            ->orderBy('stock')
            ->take(5)
            ->get();
            
        // Recent orders
        $recentOrders = Order::with(['user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Monthly sales chart data
        $monthlySalesData = Order::where('status', 'completed')
            ->whereYear('created_at', $today->year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month')
            ->map(function ($item) {
                return $item->total;
            })
            ->toArray();
            
        // Create full months array (1-12) with default 0 values
        $monthlyChartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyChartData[$i] = $monthlySalesData[$i] ?? 0;
        }
        
        // Top selling products
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_amount')
            )
            ->where('orders.status', 'completed')
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_quantity', 'desc')
            ->take(5)
            ->get();
            
        // User statistics 
        $userStats = User::select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->get()
            ->pluck('count', 'role')
            ->toArray();
        
        return view('owner.dashboard', [
            'totalSales' => $totalSales ?? 0,
            'dailySales' => $dailySales ?? 0,
            'totalProducts' => $totalProducts ?? 0,
            'totalUsers' => $totalUsers ?? 0,
            'userStats' => $userStats ?? ['owner' => 0, 'cashier' => 0, 'inventory' => 0],
            'recentOrders' => $recentOrders ?? collect([]),
            'topProducts' => $topProducts ?? collect([]),
            'lowStockProducts' => $lowStockProducts ?? collect([]),
            'monthlyChartData' => $monthlyChartData ?? [],
        ]);
    }
}
