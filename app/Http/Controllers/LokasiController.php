<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use Illuminate\Http\Request;

class LokasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Lokasi::withCount('kandangs');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lokasi', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }
        
        $lokasis = $query->orderBy('nama_lokasi')->paginate(10);
        
        return view('lokasis.index', compact('lokasis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('lokasis.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lokasi' => 'required|string|max:255|unique:lokasis,nama_lokasi',
            'deskripsi' => 'nullable|string',
        ]);

        Lokasi::create($validated);

        return redirect()->route(auth()->user()->isAdmin() ? 'admin.lokasis.index' : 'manager.lokasis.index')
                        ->with('success', 'Data lokasi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Lokasi $lokasi)
    {
        $lokasi->load('kandangs.employees');
        return view('lokasis.show', compact('lokasi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lokasi $lokasi)
    {
        return view('lokasis.edit', compact('lokasi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lokasi $lokasi)
    {
        $validated = $request->validate([
            'nama_lokasi' => 'required|string|max:255|unique:lokasis,nama_lokasi,' . $lokasi->id,
            'deskripsi' => 'nullable|string',
        ]);

        $lokasi->update($validated);

        return redirect()->route(auth()->user()->isAdmin() ? 'admin.lokasis.index' : 'manager.lokasis.index')
                        ->with('success', 'Data lokasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lokasi $lokasi)
    {
        // Check if lokasi has kandangs
        if ($lokasi->kandangs()->count() > 0) {
            return redirect()->route(auth()->user()->isAdmin() ? 'admin.lokasis.index' : 'manager.lokasis.index')
                            ->with('error', 'Tidak dapat menghapus lokasi yang masih memiliki kandang.');
        }

        $lokasi->delete();

        return redirect()->route(auth()->user()->isAdmin() ? 'admin.lokasis.index' : 'manager.lokasis.index')
                        ->with('success', 'Data lokasi berhasil dihapus.');
    }
}
