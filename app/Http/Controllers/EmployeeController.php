<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Kandang;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Employee::with(['kandang', 'kandang.lokasi']);

        // Admin can only see karyawan (not mandor)
        if (auth()->user()->isAdmin()) {
            $query->where('jabatan', 'karyawan');
        }

        // Filter by kandang if provided
        if ($request->filled('kandang_id')) {
            $query->where('kandang_id', $request->kandang_id);
        }

        // Search by nama if provided
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama', 'like', "%{$search}%");
        }

        $employees = $query->orderBy('kandang_id')->orderBy('nama')->paginate(10);
        
        // Get kandangs for filter dropdown
        $kandangs = Kandang::with('lokasi')->orderBy('nama_kandang')->get();
        
        return view('employees.index', compact('employees', 'kandangs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kandangs = Kandang::with('lokasi')->orderBy('nama_kandang')->get();
        return view('employees.create', compact('kandangs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'gaji' => 'required|numeric|min:0',
            'jabatan' => 'nullable|string|in:karyawan,mandor',
            'kandang_id' => 'nullable|exists:kandangs,id',
            'lokasi_kerja' => 'nullable|string|max:255',
        ]);

        // Rename gaji to gaji_pokok for database
        $validated['gaji_pokok'] = $validated['gaji'];
        unset($validated['gaji']);

        // Admin cannot create mandor employees
        if (auth()->user()->isAdmin() && isset($validated['jabatan']) && $validated['jabatan'] === 'mandor') {
            return redirect()->back()
                            ->with('error', 'Admin tidak dapat membuat karyawan dengan role mandor.')
                            ->withInput();
        }

        // Set default jabatan to karyawan if not specified
        if (!isset($validated['jabatan'])) {
            $validated['jabatan'] = 'karyawan';
        }

        Employee::create($validated);

        return redirect()->route(auth()->user()->isAdmin() ? 'admin.employees.index' : 'manager.employees.index')
                        ->with('success', 'Data karyawan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        // Admin cannot view mandor employees
        if (auth()->user()->isAdmin() && $employee->jabatan === 'mandor') {
            abort(403, 'Admin tidak dapat melihat data karyawan mandor.');
        }

        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        // Admin cannot edit mandor employees
        if (auth()->user()->isAdmin() && $employee->jabatan === 'mandor') {
            abort(403, 'Admin tidak dapat mengedit data karyawan mandor.');
        }

        return view('employees.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'gaji' => 'required|numeric|min:0',
            'jabatan' => 'nullable|string|in:karyawan,mandor',
        ]);

        // Rename gaji to gaji_pokok for database
        $validated['gaji_pokok'] = $validated['gaji'];
        unset($validated['gaji']);

        // Admin cannot change employee role to mandor
        if (auth()->user()->isAdmin() && isset($validated['jabatan']) && $validated['jabatan'] === 'mandor') {
            return redirect()->back()
                            ->with('error', 'Admin tidak dapat mengubah role karyawan menjadi mandor.')
                            ->withInput();
        }

        // Admin cannot change existing mandor employee
        if (auth()->user()->isAdmin() && $employee->jabatan === 'mandor') {
            return redirect()->back()
                            ->with('error', 'Admin tidak dapat mengubah data karyawan mandor.')
                            ->withInput();
        }

        $employee->update($validated);

        return redirect()->route(auth()->user()->isAdmin() ? 'admin.employees.index' : 'manager.employees.index')
                        ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('admin.employees.index')
                        ->with('success', 'Data karyawan berhasil dihapus.');
    }
}
