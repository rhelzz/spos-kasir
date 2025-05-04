<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InventoryItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:owner')->only(['create', 'store', 'edit', 'update', 'destroy']);
        $this->middleware('role:owner,inventory')->only(['index', 'show', 'updateStock']);
    }

    public function index(): View
    {
        $inventoryItems = InventoryItem::all();
        return view('inventory.index', compact('inventoryItems'));
    }

    public function create(): View
    {
        return view('inventory.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'stock_quantity' => 'required|numeric|min:0',
            'alert_threshold' => 'required|numeric|min:0',
            'cost_per_unit' => 'required|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        try {
            InventoryItem::create($validated);
            return redirect()->route('inventory.index')->with('success', 'Inventory item created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create inventory item: ' . $e->getMessage())->withInput();
        }
    }

    public function show(InventoryItem $inventoryItem): View
    {
        $inventoryItem->load('productIngredients.product');
        return view('inventory.show', compact('inventoryItem'));
    }

    public function edit(InventoryItem $inventoryItem): View
    {
        return view('inventory.edit', compact('inventoryItem'));
    }

    public function update(Request $request, InventoryItem $inventoryItem): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'stock_quantity' => 'required|numeric|min:0',
            'alert_threshold' => 'required|numeric|min:0',
            'cost_per_unit' => 'required|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        try {
            $inventoryItem->update($validated);
            return redirect()->route('inventory.index')->with('success', 'Inventory item updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update inventory item: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(InventoryItem $inventoryItem): RedirectResponse
    {
        try {
            $inventoryItem->delete();
            return redirect()->route('inventory.index')->with('success', 'Inventory item deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete inventory item: ' . $e->getMessage());
        }
    }

    public function updateStock(Request $request, InventoryItem $inventoryItem): RedirectResponse
    {
        $validated = $request->validate([
            'adjustment' => 'required|numeric',
            'reason' => 'required|string|max:255',
        ]);

        try {
            $inventoryItem->stock_quantity += $validated['adjustment'];
            $inventoryItem->notes = ($inventoryItem->notes ? $inventoryItem->notes . "\n" : '') . 
                date('Y-m-d H:i:s') . ": " . $validated['reason'] . " (" . $validated['adjustment'] . " " . $inventoryItem->unit . ")";
            $inventoryItem->save();
            
            return redirect()->route('inventory.index')->with('success', 'Stock updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update stock: ' . $e->getMessage())->withInput();
        }
    }
}
