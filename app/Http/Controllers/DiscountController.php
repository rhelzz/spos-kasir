<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Discount;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DiscountController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:owner')->except(['index']);
        $this->middleware('role:owner,cashier')->only(['index']);
    }

    public function index(): View
    {
        $discounts = Discount::all();
        return view('discounts.index', compact('discounts'));
    }

    public function create(): View
    {
        return view('discounts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'is_active' => 'sometimes|boolean',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'description' => 'nullable|string',
        ]);

        try {
            Discount::create([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'value' => $validated['value'],
                'is_active' => $request->boolean('is_active', true),
                'valid_from' => $validated['valid_from'] ?? null,
                'valid_until' => $validated['valid_until'] ?? null,
                'description' => $validated['description'] ?? null,
            ]);
            return redirect()->route('discounts.index')->with('success', 'Discount created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create discount: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Discount $discount): View
    {
        return view('discounts.edit', compact('discount'));
    }

    public function update(Request $request, Discount $discount): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'is_active' => 'sometimes|boolean',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'description' => 'nullable|string',
        ]);

        try {
            $discount->update([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'value' => $validated['value'],
                'is_active' => $request->boolean('is_active', true),
                'valid_from' => $validated['valid_from'] ?? null,
                'valid_until' => $validated['valid_until'] ?? null,
                'description' => $validated['description'] ?? null,
            ]);
            return redirect()->route('discounts.index')->with('success', 'Discount updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update discount: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Discount $discount): RedirectResponse
    {
        try {
            $discount->delete();
            return redirect()->route('discounts.index')->with('success', 'Discount deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete discount: ' . $e->getMessage());
        }
    }
}
