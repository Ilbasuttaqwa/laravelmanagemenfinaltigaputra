<?php

namespace App\Http\Controllers;

use App\Models\Kandang;
use App\Models\Lokasi;
use Illuminate\Http\Request;

class KandangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Kandang::with(['lokasi']);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_kandang', 'like', "%{$search}%")
                  ->orWhereHas('lokasi', function($q) use ($search) {
                      $q->where('nama_lokasi', 'like', "%{$search}%");
                  });
            });
        }
        
        $kandangs = $query->orderBy('nama_kandang')->paginate(10);
        
        return view('kandangs.index', compact('kandangs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lokasis = Lokasi::orderBy('nama_lokasi')->get();
        return view('kandangs.create', compact('lokasis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kandang' => 'required|string|max:255',
            'lokasi_id' => 'required|exists:lokasis,id',
        ]);

        Kandang::create([
            'nama_kandang' => $validated['nama_kandang'],
            'lokasi_id' => $validated['lokasi_id'],
        ]);
        
        // Clear cache to ensure real-time updates
        \Cache::forget('kandangs_data');
        \Cache::forget('pembibitans_data');

        return redirect()->route(auth()->user()->isManager() ? 'manager.kandangs.index' : 'admin.kandangs.index')
                        ->with('success', 'Data kandang berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kandang $kandang)
    {
        $kandang->load(['lokasi']);
        return view('kandangs.show', compact('kandang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kandang $kandang)
    {
        $lokasis = Lokasi::orderBy('nama_lokasi')->get();
        $kandang->load(['lokasi']);
        return view('kandangs.edit', compact('kandang', 'lokasis'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kandang $kandang)
    {
        $validated = $request->validate([
            'nama_kandang' => 'required|string|max:255',
            'lokasi_id' => 'required|exists:lokasis,id',
        ]);

        $kandang->update([
            'nama_kandang' => $validated['nama_kandang'],
            'lokasi_id' => $validated['lokasi_id'],
        ]);

        return redirect()->route(auth()->user()->isManager() ? 'manager.kandangs.index' : 'admin.kandangs.index')
                        ->with('success', 'Data kandang berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kandang $kandang)
    {
        $kandang->delete();

        return redirect()->route(auth()->user()->isManager() ? 'manager.kandangs.index' : 'admin.kandangs.index')
                        ->with('success', 'Data kandang berhasil dihapus.');
    }
}
