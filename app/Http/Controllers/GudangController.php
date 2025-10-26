<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use App\Services\AutoSyncGajiService;
use Illuminate\Http\Request;

class GudangController extends Controller
{
    public function index(Request $request)
    {
        // Clear cache untuk memastikan data fresh
        \Cache::forget('gudangs_data');
        \Cache::forget('employees_data');
        
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
                        ->with('success', 'Data karyawan gudang berhasil ditambahkan.');
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

        // Simpan gaji lama untuk perbandingan
        $oldGaji = $gudang->gaji;
        
        $gudang->update($validated);

        // Auto-sync gaji jika gaji berubah
        if ($oldGaji != $validated['gaji']) {
            $autoSyncService = new AutoSyncGajiService();
            $syncResult = $autoSyncService->syncGudangGaji(
                $gudang->id, 
                $validated['gaji']
            );
            
            if ($syncResult['success']) {
                $message = "Data karyawan gudang berhasil diperbarui. Gaji otomatis disinkronkan untuk {$syncResult['updated_count']} absensi.";
            } else {
                $message = "Data karyawan gudang berhasil diperbarui, namun gagal sinkronisasi gaji: {$syncResult['message']}";
            }
        } else {
            $message = "Data karyawan gudang berhasil diperbarui.";
        }

        return redirect()->route(auth()->user()->isManager() ? 'manager.gudangs.index' : 'admin.gudangs.index')
                        ->with('success', $message);
    }

    public function destroy(Gudang $gudang)
    {
        $gudang->delete();
        
        // Clear all caches after deletion
        \Cache::flush();

        return redirect()->route(auth()->user()->isManager() ? 'manager.gudangs.index' : 'admin.gudangs.index')
                        ->with('success', 'Data karyawan gudang berhasil dihapus.');
    }
}