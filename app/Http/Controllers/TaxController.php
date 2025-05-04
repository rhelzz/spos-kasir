<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tax;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TaxController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:owner')->except(['index']);
        $this->middleware('role:owner,cashier')->only(['index']);
    }

    public function index(): View
    {
        $taxes = Tax::all();
        return view('taxes.index', compact('taxes'));
    }

    public function create(): View
    {
        return view('taxes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'sometimes|boolean',
            'description' => 'nullable|string',
        ]);

        try {
            Tax::create([
                'name' => $validated['name'],
                'rate' => $validated['rate'],
                'is_active' => $request->boolean('is_active', true),
                'description' => $validated['description'] ?? null,
            ]);
            return redirect()->route('taxes.index')->with('success', 'Tax created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create tax: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Tax $tax): View
    {
        return view('taxes.edit', compact('tax'));
    }

    public function update(Request $request, Tax $tax): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'sometimes|boolean',
            'description' => 'nullable|string',
        ]);

        try {
            $tax->update([
                'name' => $validated['name'],
                'rate' => $validated['rate'],
                'is_active' => $request->boolean('is_active', true),
                'description' => $validated['description'] ?? null,
            ]);
            return redirect()->route('taxes.index')->with('success', 'Tax updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update tax: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Tax $tax): RedirectResponse
    {
        try {
            $tax->delete();
            return redirect()->route('taxes.index')->with('success', 'Tax deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete tax: ' . $e->getMessage());
        }
    }
}
