<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Employee;
use App\Models\Gudang;
use App\Models\Mandor;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $query = Absensi::with(['employee']);
        
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
        $employees = Employee::orderBy('nama')->get();
        
        return view('absensis.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:full,setengah_hari',
        ]);

        $data = [
            'employee_id' => $validated['employee_id'],
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
        return view('absensis.show', compact('absensi'));
    }

    public function edit(Absensi $absensi)
    {
        $employees = Employee::orderBy('nama')->get();
        
        return view('absensis.edit', compact('absensi', 'employees'));
    }

    public function update(Request $request, Absensi $absensi)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:full,setengah_hari',
        ]);

        $data = [
            'employee_id' => $validated['employee_id'],
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
        $employees = Employee::select('id', 'nama', 'role')->get();
        return response()->json($employees);
    }
}