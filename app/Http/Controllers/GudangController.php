<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use Illuminate\Http\Request;

class GudangController extends Controller
{
    public function index(Request $request)
    {
        $query = Gudang::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama', 'like', "%{$search}%");
        }
        
        $gudangs = $query->orderBy('nama')->paginate(10);
        
        return view('gudangs.index', compact('gudangs'));
    }

    public function create()
    {
        return view('gudangs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'gaji' => 'required|numeric|min:0',
        ]);

        Gudang::create($validated);

        return redirect()->route(auth()->user()->isManager() ? 'manager.gudangs.index' : 'admin.gudangs.index')
                        ->with('success', 'Data gudang berhasil ditambahkan.');
    }

    public function show(Gudang $gudang)
    {
        return view('gudangs.show', compact('gudang'));
    }

    public function edit(Gudang $gudang)
    {
        return view('gudangs.edit', compact('gudang'));
    }

    public function update(Request $request, Gudang $gudang)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'gaji' => 'required|numeric|min:0',
        ]);

        $gudang->update($validated);

        return redirect()->route(auth()->user()->isManager() ? 'manager.gudangs.index' : 'admin.gudangs.index')
                        ->with('success', 'Data gudang berhasil diperbarui.');
    }

    public function destroy(Gudang $gudang)
    {
        $gudang->delete();

        return redirect()->route(auth()->user()->isManager() ? 'manager.gudangs.index' : 'admin.gudangs.index')
                        ->with('success', 'Data gudang berhasil dihapus.');
    }
}