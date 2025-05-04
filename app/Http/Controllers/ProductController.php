<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\ProductIngredient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:owner')->except(['index', 'show']);
    }

    public function index(): View
    {
        $products = Product::with('category', 'ingredients.inventoryItem')->get();
        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::all();
        $inventoryItems = InventoryItem::all();
        return view('products.create', compact('categories', 'inventoryItems'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:products',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'sometimes|boolean',
            'image' => 'nullable|image|max:2048',
            'ingredients' => 'sometimes|array',
            'ingredients.*' => 'exists:inventory_items,id',
            'quantities' => 'sometimes|array',
            'quantities.*' => 'numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Handle image upload if present
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
            }

            // Create product
            $product = Product::create([
                'name' => $validated['name'],
                'sku' => $validated['sku'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'category_id' => $validated['category_id'],
                'is_active' => $request->boolean('is_active', true),
                'image_path' => $imagePath,
            ]);

            // Add ingredients if provided
            if ($request->has('ingredients')) {
                foreach ($request->ingredients as $index => $ingredientId) {
                    ProductIngredient::create([
                        'product_id' => $product->id,
                        'inventory_item_id' => $ingredientId,
                        'quantity' => $request->quantities[$index] ?? 0,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('products.index')->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create product: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Product $product): View
    {
        $product->load('category', 'ingredients.inventoryItem');
        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $product->load('ingredients.inventoryItem');
        $categories = Category::all();
        $inventoryItems = InventoryItem::all();
        return view('products.edit', compact('product', 'categories', 'inventoryItems'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $product->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'sometimes|boolean',
            'image' => 'nullable|image|max:2048',
            'ingredients' => 'sometimes|array',
            'ingredients.*' => 'exists:inventory_items,id',
            'quantities' => 'sometimes|array',
            'quantities.*' => 'numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Handle image upload if present
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
                $product->image_path = $imagePath;
            }

            // Update product
            $product->update([
                'name' => $validated['name'],
                'sku' => $validated['sku'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'category_id' => $validated['category_id'],
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Update ingredients if provided
            if ($request->has('ingredients')) {
                // Remove existing ingredients
                $product->ingredients()->delete();
                
                // Add new ingredients
                foreach ($request->ingredients as $index => $ingredientId) {
                    ProductIngredient::create([
                        'product_id' => $product->id,
                        'inventory_item_id' => $ingredientId,
                        'quantity' => $request->quantities[$index] ?? 0,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('products.index')->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update product: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Product $product): RedirectResponse
    {
        try {
            $product->delete();
            return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }
}
