<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Kandang;
use App\Models\Pembibitan;
use App\Models\User;
use Illuminate\Http\Request;

class KaryawanKandangController extends Controller
{
    /**
     * Get current authenticated user
     * @return User|null
     */
    private function getCurrentUser(): ?User
    {
        return auth()->user();
    }
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
        $pembibitans = Pembibitan::with(['kandang', 'kandang.lokasi'])->orderBy('judul')->get();
        return view('karyawan-kandangs.create', compact('pembibitans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'gaji_pokok' => 'required|numeric|min:0',
            'pembibitan_id' => 'required|exists:pembibitans,id',
        ]);

        // Get kandang_id from pembibitan
        $pembibitan = Pembibitan::find($validated['pembibitan_id']);

        Employee::create([
            'nama' => $validated['nama'],
            'gaji_pokok' => $validated['gaji_pokok'],
            'jabatan' => 'karyawan',
            'kandang_id' => $pembibitan->kandang_id,
        ]);

        return redirect()->route($this->getCurrentUser()?->isAdmin() ? 'admin.karyawan-kandangs.index' : 'manager.karyawan-kandangs.index')
                        ->with('success', 'Data karyawan kandang berhasil ditambahkan.');
    }

    public function show(Employee $karyawanKandang)
    {
        $karyawanKandang->load(['kandang', 'kandang.lokasi']);
        return view('karyawan-kandangs.show', compact('karyawanKandang'));
    }

    public function edit(Employee $karyawanKandang)
    {
        $pembibitans = Pembibitan::with(['kandang', 'kandang.lokasi'])->orderBy('judul')->get();
        return view('karyawan-kandangs.edit', compact('karyawanKandang', 'pembibitans'));
    }

    public function update(Request $request, Employee $karyawanKandang)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'gaji_pokok' => 'required|numeric|min:0',
            'pembibitan_id' => 'required|exists:pembibitans,id',
        ]);

        // Get kandang_id from pembibitan
        $pembibitan = Pembibitan::find($validated['pembibitan_id']);

        $karyawanKandang->update([
            'nama' => $validated['nama'],
            'gaji_pokok' => $validated['gaji_pokok'],
            'kandang_id' => $pembibitan->kandang_id,
        ]);

        return redirect()->route($this->getCurrentUser()?->isAdmin() ? 'admin.karyawan-kandangs.index' : 'manager.karyawan-kandangs.index')
                        ->with('success', 'Data karyawan kandang berhasil diperbarui.');
    }

    public function destroy(Employee $karyawanKandang)
    {
        $karyawanKandang->delete();
        return redirect()->route($this->getCurrentUser()?->isAdmin() ? 'admin.karyawan-kandangs.index' : 'manager.karyawan-kandangs.index')
                        ->with('success', 'Data karyawan kandang berhasil dihapus.');
    }
}
