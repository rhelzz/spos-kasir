<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Routing\Controller;

class TableController extends Controller
{
    public function index(): View
    {
        $tables = Table::all();
        return view('tables.index', compact('tables'));
    }

    public function create(): View
    {
        return view('tables.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tables',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,reserved,needs_cleaning',
            'notes' => 'nullable|string',
        ]);

        Table::create($validated);

        return redirect()->route('tables.index')->with('success', 'Meja berhasil dibuat.');
    }

    public function show(Table $table): View
    {
        $table->load('orders');
        return view('tables.show', compact('table'));
    }

    public function edit(Table $table): View
    {
        return view('tables.edit', compact('table'));
    }

    public function update(Request $request, Table $table): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tables,name,' . $table->id,
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,reserved,needs_cleaning',
            'notes' => 'nullable|string',
        ]);

        $table->update($validated);

        return redirect()->route('tables.index')->with('success', 'Meja berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Table $table): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:available,occupied,reserved,needs_cleaning',
        ]);

        $table->status = $validated['status'];
        $table->save();

        return back()->with('success', 'Status meja berhasil diperbarui.');
    }

    public function destroy(Table $table): RedirectResponse
    {
        // Cek jika meja sedang digunakan dalam pesanan
        if ($table->status === 'occupied') {
            return back()->with('error', 'Meja tidak dapat dihapus karena sedang digunakan.');
        }

        $table->delete();
        return redirect()->route('tables.index')->with('success', 'Meja berhasil dihapus.');
    }
}
