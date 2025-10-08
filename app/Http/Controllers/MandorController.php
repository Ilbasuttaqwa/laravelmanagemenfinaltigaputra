<?php

namespace App\Http\Controllers;

use App\Models\Mandor;
use Illuminate\Http\Request;

class MandorController extends Controller
{
    public function index(Request $request)
    {
        $query = Mandor::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama', 'like', "%{$search}%");
        }
        
        $mandors = $query->orderBy('nama')->paginate(10);
        
        return view('mandors.index', compact('mandors'));
    }

    public function create()
    {
        return view('mandors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'gaji' => 'required|numeric|min:0',
        ]);

        Mandor::create($validated);

        return redirect()->route(auth()->user()->isManager() ? 'manager.mandors.index' : 'admin.mandors.index')
                        ->with('success', 'Data mandor berhasil ditambahkan.');
    }

    public function show(Mandor $mandor)
    {
        return view('mandors.show', compact('mandor'));
    }

    public function edit(Mandor $mandor)
    {
        return view('mandors.edit', compact('mandor'));
    }

    public function update(Request $request, Mandor $mandor)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'gaji' => 'required|numeric|min:0',
        ]);

        $mandor->update($validated);

        return redirect()->route(auth()->user()->isManager() ? 'manager.mandors.index' : 'admin.mandors.index')
                        ->with('success', 'Data mandor berhasil diperbarui.');
    }

    public function destroy(Mandor $mandor)
    {
        $mandor->delete();

        return redirect()->route(auth()->user()->isManager() ? 'manager.mandors.index' : 'admin.mandors.index')
                        ->with('success', 'Data mandor berhasil dihapus.');
    }
}