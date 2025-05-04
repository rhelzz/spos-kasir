<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Table;
use App\Models\Tax;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:owner,cashier');
    }

    public function index(): View
    {
        $orders = Order::with('table', 'user', 'tax', 'discount', 'items.product')->latest()->paginate(15);
        return view('orders.index', compact('orders'));
    }

    public function create(): View
    {
        $tables = Table::where('status', 'available')->get();
        $products = Product::where('is_active', true)->get();
        $taxes = Tax::where('is_active', true)->get();
        $discounts = Discount::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now());
            })
            ->get();
        
        return view('orders.create', compact('tables', 'products', 'taxes', 'discounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_type' => 'required|in:dine_in,take_away',
            'table_id' => 'required_if:order_type,dine_in|nullable|exists:tables,id',
            'tax_id' => 'nullable|exists:taxes,id',
            'discount_id' => 'nullable|exists:discounts,id',
            'service_charge' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*' => 'exists:products,id',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'integer|min:1',
            'item_notes' => 'nullable|array',
            'item_notes.*' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Check if all products have enough stock
            foreach ($request->products as $index => $productId) {
                $product = Product::find($productId);
                $quantity = $request->quantities[$index];
                
                if (!$product->hasEnoughStock()) {
                    DB::rollBack();
                    return back()->with('error', "Product '{$product->name}' doesn't have enough stock to complete this order.")->withInput();
                }
            }

            // Create order
            $order = Order::create([
                'order_type' => $validated['order_type'],
                'table_id' => $validated['order_type'] === 'dine_in' ? $validated['table_id'] : null,
                'user_id' => Auth::id(),
                'tax_id' => $validated['tax_id'] ?? null,
                'discount_id' => $validated['discount_id'] ?? null,
                'service_charge' => $validated['service_charge'] ?? 0,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Add order items
            foreach ($request->products as $index => $productId) {
                $product = Product::find($productId);
                $quantity = $request->quantities[$index];
                $notes = $request->item_notes[$index] ?? null;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $quantity,
                    'notes' => $notes,
                ]);
            }

            // Update table status if dine in
            if ($validated['order_type'] === 'dine_in' && $validated['table_id']) {
                $table = Table::find($validated['table_id']);
                $table->setOccupied();
            }

            // Calculate totals
            $order->calculateTotals();

            DB::commit();
            return redirect()->route('orders.show', $order)->with('success', 'Order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create order: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Order $order): View
    {
        $order->load('table', 'user', 'tax', 'discount', 'items.product', 'payment');
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order): View|RedirectResponse
    {
        if ($order->status !== 'pending') {
            return redirect()->route('orders.show', $order)->with('error', 'Only pending orders can be edited.');
        }

        $order->load('items.product');
        $tables = Table::where(function ($query) use ($order) {
            $query->where('status', 'available')
                ->orWhere('id', $order->table_id);
        })->get();
        
        $products = Product::where('is_active', true)->get();
        $taxes = Tax::where('is_active', true)->get();
        $discounts = Discount::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now());
            })
            ->get();
        
        return view('orders.edit', compact('order', 'tables', 'products', 'taxes', 'discounts'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        if ($order->status !== 'pending') {
            return redirect()->route('orders.show', $order)->with('error', 'Only pending orders can be updated.');
        }

        $validated = $request->validate([
            'order_type' => 'required|in:dine_in,take_away',
            'table_id' => 'required_if:order_type,dine_in|nullable|exists:tables,id',
            'tax_id' => 'nullable|exists:taxes,id',
            'discount_id' => 'nullable|exists:discounts,id',
            'service_charge' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*' => 'exists:products,id',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'integer|min:1',
            'item_notes' => 'nullable|array',
            'item_notes.*' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Update order details
            $order->update([
                'order_type' => $validated['order_type'],
                'table_id' => $validated['order_type'] === 'dine_in' ? $validated['table_id'] : null,
                'tax_id' => $validated['tax_id'] ?? null,
                'discount_id' => $validated['discount_id'] ?? null,
                'service_charge' => $validated['service_charge'] ?? 0,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Handle table change if needed
            if ($order->isDirty('table_id')) {
                // If previous table exists, set it available
                if ($order->getOriginal('table_id')) {
                    Table::find($order->getOriginal('table_id'))->setAvailable();
                }
                // If new table, set it occupied
                if ($order->table_id) {
                    Table::find($order->table_id)->setOccupied();
                }
            }

            // Remove all existing items and restore their stock
            foreach ($order->items as $item) {
                if ($item->product) {
                    // Restore stock by adding back what was reduced
                    foreach ($item->product->ingredients as $ingredient) {
                        $inventoryItem = $ingredient->inventoryItem;
                        $inventoryItem->stock_quantity += ($ingredient->quantity * $item->quantity);
                        $inventoryItem->save();
                    }
                }
            }
            $order->items()->delete();

            // Check if all new products have enough stock
            foreach ($request->products as $index => $productId) {
                $product = Product::find($productId);
                if (!$product->hasEnoughStock()) {
                    DB::rollBack();
                    return back()->with('error', "Product '{$product->name}' doesn't have enough stock.")->withInput();
                }
            }

            // Add new order items
            foreach ($request->products as $index => $productId) {
                $product = Product::find($productId);
                $quantity = $request->quantities[$index];
                $notes = $request->item_notes[$index] ?? null;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $quantity,
                    'notes' => $notes,
                ]);
            }

            // Recalculate totals
            $order->calculateTotals();

            DB::commit();
            return redirect()->route('orders.show', $order)->with('success', 'Order updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update order: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Order $order): RedirectResponse
    {
        if ($order->status !== 'pending') {
            return redirect()->route('orders.show', $order)->with('error', 'Only pending orders can be cancelled.');
        }

        try {
            DB::beginTransaction();

            // Restore stock for each item
            foreach ($order->items as $item) {
                if ($item->product) {
                    // Restore stock by adding back what was reduced
                    foreach ($item->product->ingredients as $ingredient) {
                        $inventoryItem = $ingredient->inventoryItem;
                        $inventoryItem->stock_quantity += ($ingredient->quantity * $item->quantity);
                        $inventoryItem->save();
                    }
                }
            }

            // If table was occupied by this order, set it available
            if ($order->table) {
                $order->table->setAvailable();
            }

            // Mark order as cancelled
            $order->status = 'cancelled';
            $order->save();

            DB::commit();
            return redirect()->route('orders.index')->with('success', 'Order cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel order: ' . $e->getMessage());
        }
    }
}
