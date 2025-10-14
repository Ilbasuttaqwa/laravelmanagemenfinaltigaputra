<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Employee;
use App\Models\Gudang;
use App\Models\UnifiedEmployee;
use App\Services\MasterSyncService;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $query = Absensi::with(['employee']);
        
        // Admin can only see absensi for karyawan (not mandor)
        if (auth()->user()->isAdmin()) {
            $query->whereHas('employee', function($employeeQuery) {
                $employeeQuery->where('role', 'karyawan');
            });
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('status', 'like', "%{$search}%")
                  ->orWhereHas('employee', function($employeeQuery) use ($search) {
                      $employeeQuery->where('nama', 'like', "%{$search}%");
                  });
            });
        }
        
        $absensis = $query->orderBy('id', 'desc')->get();
        
        return view('absensis.index', compact('absensis'));
    }

    public function create()
    {
        // Sync all master data first
        MasterSyncService::syncAll();
        
        // Get unified employees based on user role
        $query = UnifiedEmployee::orderBy('nama');
        
        if (auth()->user()->isAdmin()) {
            // Admin can only see non-mandor employees
            $query->where('role', '!=', 'mandor');
        }
        
        $unifiedEmployees = $query->get();
        
        return view('absensis.create', compact('unifiedEmployees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'unified_employee_id' => 'required|exists:unified_employees,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:full,setengah_hari',
        ]);

        // Get unified employee
        $unifiedEmployee = UnifiedEmployee::find($validated['unified_employee_id']);
        
        if (!$unifiedEmployee) {
            return redirect()->back()
                ->with('error', 'Karyawan tidak ditemukan.')
                ->withInput();
        }

        // Admin cannot create absensi for mandor employees
        if (auth()->user()->isAdmin() && $unifiedEmployee->role === 'mandor') {
            return redirect()->back()
                ->with('error', 'Admin tidak dapat membuat absensi untuk karyawan mandor.')
                ->withInput();
        }

        // Check for duplicate attendance on the same date
        $existingAbsensi = Absensi::where('source_type', $unifiedEmployee->source_type)
            ->where('source_id', $unifiedEmployee->source_id)
            ->whereDate('tanggal', $validated['tanggal'])
            ->first();

        if ($existingAbsensi) {
            return redirect()->back()
                ->with('error', 'Data absensi untuk karyawan ini pada tanggal tersebut sudah ada.')
                ->withInput();
        }

        $data = [
            'employee_id' => null, // No longer needed
            'source_type' => $unifiedEmployee->source_type,
            'source_id' => $unifiedEmployee->source_id,
            'nama_karyawan' => $unifiedEmployee->nama,
            'role_karyawan' => $unifiedEmployee->role,
            'gaji_karyawan' => $unifiedEmployee->gaji,
            'tanggal' => $validated['tanggal'],
            'status' => $validated['status'],
        ];

        $absensi = Absensi::create($data);

        // Update monthly attendance report
        $this->updateMonthlyReport($absensi);

        return redirect()->route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index')
                        ->with('success', 'Data absensi berhasil ditambahkan.');
    }

    public function show(Absensi $absensi)
    {
        $absensi->load(['employee']);
        
        // Admin cannot view absensi for mandor employees
        if (auth()->user()->isAdmin() && $absensi->employee && $absensi->employee->role === 'mandor') {
            abort(403, 'Admin tidak dapat melihat data absensi karyawan mandor.');
        }
        
        return view('absensis.show', compact('absensi'));
    }

    public function edit(Absensi $absensi)
    {
        // Admin cannot edit absensi for mandor employees
        if (auth()->user()->isAdmin() && $absensi->employee && $absensi->employee->role === 'mandor') {
            abort(403, 'Admin tidak dapat mengedit data absensi karyawan mandor.');
        }
        
        // Get employees from employees table
        $query = Employee::orderBy('nama');
        
        // Admin can only see karyawan (not mandor)
        if (auth()->user()->isAdmin()) {
            $query->where('role', 'karyawan');
        }
        
        $employees = $query->get();
        
        // Get gudang employees (karyawan gudang)
        $gudangEmployees = collect();
        if (auth()->user()->isManager()) {
            $gudangEmployees = \App\Models\Gudang::orderBy('nama')->get()->map(function($gudang) {
                return (object) [
                    'id' => 'gudang_' . $gudang->id,
                    'nama' => $gudang->nama,
                    'role' => 'karyawan_gudang',
                    'gaji' => $gudang->gaji,
                    'source' => 'gudang'
                ];
            });
        }
        
        // Get mandor employees
        $mandorEmployees = collect();
        if (auth()->user()->isManager()) {
            $mandorEmployees = \App\Models\Mandor::orderBy('nama')->get()->map(function($mandor) {
                return (object) [
                    'id' => 'mandor_' . $mandor->id,
                    'nama' => $mandor->nama,
                    'role' => 'mandor',
                    'gaji' => $mandor->gaji,
                    'source' => 'mandor'
                ];
            });
        }
        
        // Combine all employees
        $allEmployees = $employees->map(function($employee) {
            return (object) [
                'id' => 'employee_' . $employee->id,
                'nama' => $employee->nama,
                'role' => $employee->role,
                'gaji' => $employee->gaji,
                'source' => 'employee'
            ];
        })->concat($gudangEmployees)->concat($mandorEmployees)->sortBy('nama');
        
        return view('absensis.edit', compact('absensi', 'allEmployees'));
    }

    public function update(Request $request, Absensi $absensi)
    {
        $validated = $request->validate([
            'employee_id' => 'required|string',
            'tanggal' => 'required|date',
            'status' => 'required|in:full,setengah_hari',
        ]);

        // Parse employee_id to determine source and actual ID
        $employeeId = $validated['employee_id'];
        $actualEmployeeId = null;
        $employeeRole = null;
        $employeeName = null;

        if (str_starts_with($employeeId, 'employee_')) {
            // From employees table
            $actualEmployeeId = str_replace('employee_', '', $employeeId);
            $employee = Employee::find($actualEmployeeId);
            if ($employee) {
                $employeeRole = $employee->role;
                $employeeName = $employee->nama;
            }
        } elseif (str_starts_with($employeeId, 'gudang_')) {
            // From gudangs table - create temporary employee record
            $gudangId = str_replace('gudang_', '', $employeeId);
            $gudang = \App\Models\Gudang::find($gudangId);
            if ($gudang) {
                // Create or find employee record for gudang
                $employee = Employee::firstOrCreate(
                    ['nama' => $gudang->nama, 'role' => 'karyawan_gudang'],
                    ['gaji' => $gudang->gaji]
                );
                $actualEmployeeId = $employee->id;
                $employeeRole = 'karyawan_gudang';
                $employeeName = $gudang->nama;
            }
        } elseif (str_starts_with($employeeId, 'mandor_')) {
            // From mandors table - create temporary employee record
            $mandorId = str_replace('mandor_', '', $employeeId);
            $mandor = \App\Models\Mandor::find($mandorId);
            if ($mandor) {
                // Create or find employee record for mandor
                $employee = Employee::firstOrCreate(
                    ['nama' => $mandor->nama, 'role' => 'mandor'],
                    ['gaji' => $mandor->gaji]
                );
                $actualEmployeeId = $employee->id;
                $employeeRole = 'mandor';
                $employeeName = $mandor->nama;
            }
        }

        if (!$actualEmployeeId) {
            return redirect()->back()
                ->with('error', 'Karyawan tidak ditemukan.')
                ->withInput();
        }

        // Admin cannot update absensi for mandor employees
        if (auth()->user()->isAdmin() && $employeeRole === 'mandor') {
            return redirect()->back()
                ->with('error', 'Admin tidak dapat mengubah data absensi karyawan mandor.')
                ->withInput();
        }

        $data = [
            'employee_id' => $actualEmployeeId,
            'tanggal' => $validated['tanggal'],
            'status' => $validated['status'],
        ];

        $absensi->update($data);

        // Update monthly attendance report
        $this->updateMonthlyReport($absensi);

        return redirect()->route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index')
                        ->with('success', 'Data absensi berhasil diperbarui.');
    }

    public function destroy(Absensi $absensi)
    {
        $absensi->delete();

        return redirect()->route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index')
                        ->with('success', 'Data absensi berhasil dihapus.');
    }

    private function updateMonthlyReport($absensi)
    {
        $tahun = $absensi->tanggal->year;
        $bulan = $absensi->tanggal->month;
        
        // Get employee information
        $employee = $absensi->employee;
        if (!$employee) {
            return;
        }
        
        $karyawanId = $employee->id;
        $tipeKaryawan = $employee->role ?? 'employee';
        $namaKaryawan = $employee->nama;
        
        // Find or create monthly report
        $report = \App\Models\MonthlyAttendanceReport::where('karyawan_id', $karyawanId)
            ->where('tipe_karyawan', $tipeKaryawan)
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->first();
            
        if (!$report) {
            $report = \App\Models\MonthlyAttendanceReport::create([
                'karyawan_id' => $karyawanId,
                'nama_karyawan' => $namaKaryawan,
                'tipe_karyawan' => $tipeKaryawan,
                'tahun' => $tahun,
                'bulan' => $bulan,
                'data_absensi' => json_encode([]),
                'total_hari_kerja' => $this->getWorkingDaysInMonth($tahun, $bulan),
                'total_hari_full' => 0,
                'total_hari_setengah' => 0,
                'total_hari_absen' => 0,
                'persentase_kehadiran' => 0.00,
            ]);
        }
        
        // Recalculate attendance data for the month
        $this->recalculateMonthlyAttendance($report, $tahun, $bulan, $tipeKaryawan, $karyawanId);
    }
    
    private function getWorkingDaysInMonth($tahun, $bulan)
    {
        $daysInMonth = \Carbon\Carbon::create($tahun, $bulan)->daysInMonth;
        $workingDays = 0;
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = \Carbon\Carbon::create($tahun, $bulan, $day);
            if (!$date->isWeekend()) {
                $workingDays++;
            }
        }
        
        return $workingDays;
    }
    
    private function recalculateMonthlyAttendance($report, $tahun, $bulan, $tipeKaryawan, $karyawanId)
    {
        // Get all attendance records for the month
        $absensis = Absensi::where('employee_id', $karyawanId)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();
            
        $fullDayCount = $absensis->where('status', 'full')->count();
        $halfDayCount = $absensis->where('status', 'setengah_hari')->count();
        $totalAttendance = $fullDayCount + $halfDayCount;
        $absenCount = $report->total_hari_kerja - $totalAttendance;
        
        // Calculate attendance percentage
        $persentaseKehadiran = $report->total_hari_kerja > 0 
            ? ($totalAttendance / $report->total_hari_kerja) * 100 
            : 0;
            
        // Update the report
        $report->update([
            'total_hari_full' => $fullDayCount,
            'total_hari_setengah' => $halfDayCount,
            'total_hari_absen' => $absenCount,
            'persentase_kehadiran' => round($persentaseKehadiran, 2),
        ]);
    }

    /**
     * Update existing attendance record
     */
    public function updateExisting(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:full,setengah_hari,absen',
        ]);

        // Find existing record
        $absensi = Absensi::where('employee_id', $validated['employee_id'])
            ->where('tanggal', $validated['tanggal'])
            ->first();

        if ($absensi) {
            // Update existing record
            $absensi->update(['status' => $validated['status']]);
            
            // Update monthly attendance report
            $this->updateMonthlyReport($absensi);
            
            return response()->json(['success' => true, 'message' => 'Data absensi berhasil diperbarui']);
        } else {
            // Create new record if not exists
            $data = [
                'employee_id' => $validated['employee_id'],
                'tanggal' => $validated['tanggal'],
                'status' => $validated['status'],
            ];

            $absensi = Absensi::create($data);
            $this->updateMonthlyReport($absensi);
            
            return response()->json(['success' => true, 'message' => 'Data absensi berhasil ditambahkan']);
        }
    }

    /**
     * Get employees for AJAX dropdown
     */
    public function getEmployees(Request $request)
    {
        $query = Employee::select('id', 'nama', 'role');
        
        // Admin can only see karyawan (not mandor)
        if (auth()->user()->isAdmin()) {
            $query->where('role', 'karyawan');
        }
        
        $employees = $query->get();
        return response()->json($employees);
    }
}