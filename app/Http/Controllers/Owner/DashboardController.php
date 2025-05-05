<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Category;
use App\Models\Shift;
use App\Models\InventoryItem;
use App\Models\Table;
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
        // Get time periods for filtering
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
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
            
        $yesterdaySales = Order::where('status', 'completed')
            ->whereDate('created_at', $yesterday)
            ->sum('total_amount');
            
        // Calculate growth percentage
        $salesGrowth = 0;
        if ($yesterdaySales > 0) {
            $salesGrowth = (($dailySales - $yesterdaySales) / $yesterdaySales) * 100;
        }
        
        // Count statistics
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $totalCategories = Category::count();
        $totalTables = Table::count();
        $totalUsers = User::count();
        
        // Low inventory items (using the isLowStock method from the model)
        $lowStockItems = InventoryItem::get()->filter(function($item) {
            return $item->isLowStock();
        })->take(5);
            
        // Recent orders with relationships
        $recentOrders = Order::with(['user', 'items.product', 'table', 'payment'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Current active shifts
        $activeShifts = Shift::whereNull('end_time')
            ->with('user')
            ->get();
            
        // Table status summary
        $tableStatus = [
            'available' => Table::where('status', 'available')->count(),
            'occupied' => Table::where('status', 'occupied')->count(),
            'reserved' => Table::where('status', 'reserved')->count(),
            'needs_cleaning' => Table::where('status', 'needs_cleaning')->count(),
        ];
            
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
        
        // Top selling products (by quantity)
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select(
                'products.id',
                'products.name',
                'products.category_id',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_amount')
            )
            ->where('orders.status', 'completed')
            ->groupBy('products.id', 'products.name', 'products.category_id')
            ->orderBy('total_quantity', 'desc')
            ->take(5)
            ->get();
            
        // Get category names for the top products
        foreach ($topProducts as $product) {
            $category = Category::find($product->category_id);
            $product->category_name = $category ? $category->name : 'Uncategorized';
        }
        
        // Payment method distribution
        $paymentMethods = Payment::where('status', 'completed')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount_paid) as total'))
            ->groupBy('payment_method')
            ->get();
            
        // Category distribution for sold products
        $categorySales = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_amount')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_amount', 'desc')
            ->take(5)
            ->get();
        
        // User statistics 
        $userStats = User::select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->get()
            ->pluck('count', 'role')
            ->toArray();
        
        return view('owner.dashboard', compact(
            'totalSales',
            'monthlySales',
            'dailySales',
            'salesGrowth',
            'totalProducts',
            'activeProducts',
            'totalCategories',
            'totalTables',
            'totalUsers',
            'userStats',
            'recentOrders',
            'activeShifts',
            'tableStatus',
            'lowStockItems',
            'topProducts',
            'paymentMethods',
            'categorySales',
            'monthlyChartData'
        ));
    }
}
