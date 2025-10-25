<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Kandang;
use Illuminate\Http\Request;

class KaryawanKandangController extends Controller
{
    public function index()
    {
        $karyawans = Employee::with(['kandang', 'kandang.lokasi'])
            ->where('jabatan', 'karyawan')
            ->orderBy('nama')
            ->paginate(10);

        return view('karyawan-kandangs.index', compact('karyawans'));
    }

    public function create()
    {
        $kandangs = Kandang::with('lokasi')->orderBy('nama_kandang')->get();
        return view('karyawan-kandangs.create', compact('kandangs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'gaji_pokok' => 'required|numeric|min:0',
            'kandang_id' => 'required|exists:kandangs,id',
        ]);

        Employee::create([
            'nama' => $validated['nama'],
            'gaji_pokok' => $validated['gaji_pokok'],
            'jabatan' => 'karyawan',
            'kandang_id' => $validated['kandang_id'],
        ]);

        return redirect()->route(auth()->user()->isAdmin() ? 'admin.karyawan-kandangs.index' : 'manager.karyawan-kandangs.index')
                        ->with('success', 'Data karyawan kandang berhasil ditambahkan.');
    }

    public function show(Employee $karyawanKandang)
    {
        $karyawanKandang->load(['kandang', 'kandang.lokasi']);
        return view('karyawan-kandangs.show', compact('karyawanKandang'));
    }

    public function edit(Employee $karyawanKandang)
    {
        $kandangs = Kandang::with('lokasi')->orderBy('nama_kandang')->get();
        return view('karyawan-kandangs.edit', compact('karyawanKandang', 'kandangs'));
    }

    public function update(Request $request, Employee $karyawanKandang)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'gaji_pokok' => 'required|numeric|min:0',
            'kandang_id' => 'required|exists:kandangs,id',
        ]);

        $karyawanKandang->update([
            'nama' => $validated['nama'],
            'gaji_pokok' => $validated['gaji_pokok'],
            'kandang_id' => $validated['kandang_id'],
        ]);

        return redirect()->route(auth()->user()->isAdmin() ? 'admin.karyawan-kandangs.index' : 'manager.karyawan-kandangs.index')
                        ->with('success', 'Data karyawan kandang berhasil diperbarui.');
    }

    public function destroy(Employee $karyawanKandang)
    {
        $karyawanKandang->delete();
        return redirect()->route(auth()->user()->isAdmin() ? 'admin.karyawan-kandangs.index' : 'manager.karyawan-kandangs.index')
                        ->with('success', 'Data karyawan kandang berhasil dihapus.');
    }
}
