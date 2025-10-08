<?php

namespace App\Http\Controllers;

use App\Models\Pembibitan;
use App\Models\Lokasi;
use App\Models\Kandang;
use Illuminate\Http\Request;

class PembibitanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembibitan::with(['lokasi', 'kandang']);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhereHas('lokasi', function($lokasiQuery) use ($search) {
                      $lokasiQuery->where('nama_lokasi', 'like', "%{$search}%");
                  })
                  ->orWhereHas('kandang', function($kandangQuery) use ($search) {
                      $kandangQuery->where('nama_kandang', 'like', "%{$search}%");
                  });
            });
        }
        
        $pembibitans = $query->orderBy('judul')->paginate(10);
        
        return view('pembibitans.index', compact('pembibitans'));
    }

    public function create()
    {
        $lokasis = Lokasi::orderBy('nama_lokasi')->get();
        $kandangs = Kandang::orderBy('nama_kandang')->get();
        
        return view('pembibitans.create', compact('lokasis', 'kandangs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'lokasi_id' => 'nullable|exists:lokasis,id',
            'kandang_id' => 'nullable|exists:kandangs,id',
            'tanggal_mulai' => 'required|date',
        ]);

        Pembibitan::create($validated);

        return redirect()->route(auth()->user()->isManager() ? 'manager.pembibitans.index' : 'admin.pembibitans.index')
                        ->with('success', 'Data pembibitan berhasil ditambahkan.');
    }

    public function show(Pembibitan $pembibitan)
    {
        $pembibitan->load(['lokasi', 'kandang']);
        return view('pembibitans.show', compact('pembibitan'));
    }

    public function edit(Pembibitan $pembibitan)
    {
        $lokasis = Lokasi::orderBy('nama_lokasi')->get();
        $kandangs = Kandang::orderBy('nama_kandang')->get();
        
        return view('pembibitans.edit', compact('pembibitan', 'lokasis', 'kandangs'));
    }

    public function update(Request $request, Pembibitan $pembibitan)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'lokasi_id' => 'nullable|exists:lokasis,id',
            'kandang_id' => 'nullable|exists:kandangs,id',
            'tanggal_mulai' => 'required|date',
        ]);

        $pembibitan->update($validated);

        return redirect()->route(auth()->user()->isManager() ? 'manager.pembibitans.index' : 'admin.pembibitans.index')
                        ->with('success', 'Data pembibitan berhasil diperbarui.');
    }

    public function destroy(Pembibitan $pembibitan)
    {
        $pembibitan->delete();

        return redirect()->route(auth()->user()->isManager() ? 'manager.pembibitans.index' : 'admin.pembibitans.index')
                        ->with('success', 'Data pembibitan berhasil dihapus.');
    }
}