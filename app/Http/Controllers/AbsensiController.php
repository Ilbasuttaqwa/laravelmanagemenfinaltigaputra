<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Gudang;
use App\Models\Mandor;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $query = Absensi::with(['gudang', 'mandor']);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('status', 'like', "%{$search}%")
                  ->orWhereHas('gudang', function($gudangQuery) use ($search) {
                      $gudangQuery->where('nama', 'like', "%{$search}%");
                  })
                  ->orWhereHas('mandor', function($mandorQuery) use ($search) {
                      $mandorQuery->where('nama', 'like', "%{$search}%");
                  });
            });
        }
        
        $absensis = $query->orderBy('id', 'desc')->get();
        
        return view('absensis.index', compact('absensis'));
    }

    public function create()
    {
        $gudangs = Gudang::orderBy('nama')->get();
        $mandors = Mandor::orderBy('nama')->get();
        
        return view('absensis.create', compact('gudangs', 'mandors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|string',
            'tanggal' => 'required|date',
            'status' => 'required|in:full,setengah_hari',
        ]);

        // Parse karyawan_id format: "gudang_1" or "mandor_1"
        $karyawanParts = explode('_', $validated['karyawan_id']);
        $karyawanTipe = $karyawanParts[0];
        $karyawanId = $karyawanParts[1];

        $data = [
            'tanggal' => $validated['tanggal'],
            'status' => $validated['status'],
            'gudang_id' => null,
            'mandor_id' => null,
        ];

        if ($karyawanTipe === 'gudang') {
            $data['gudang_id'] = $karyawanId;
        } else {
            $data['mandor_id'] = $karyawanId;
        }

        $absensi = Absensi::create($data);

        // Update monthly attendance report
        $this->updateMonthlyReport($absensi);

        return redirect()->route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index')
                        ->with('success', 'Data absensi berhasil ditambahkan.');
    }

    public function show(Absensi $absensi)
    {
        $absensi->load(['gudang', 'mandor']);
        return view('absensis.show', compact('absensi'));
    }

    public function edit(Absensi $absensi)
    {
        $gudangs = Gudang::orderBy('nama')->get();
        $mandors = Mandor::orderBy('nama')->get();
        
        return view('absensis.edit', compact('absensi', 'gudangs', 'mandors'));
    }

    public function update(Request $request, Absensi $absensi)
    {
        $validated = $request->validate([
            'karyawan_tipe' => 'required|in:gudang,mandor',
            'karyawan_id' => 'required|integer',
            'tanggal' => 'required|date',
            'status' => 'required|in:full,setengah_hari',
        ]);

        $karyawanTipe = $validated['karyawan_tipe'];
        $karyawanId = $validated['karyawan_id'];

        $data = [
            'tanggal' => $validated['tanggal'],
            'status' => $validated['status'],
            'gudang_id' => null,
            'mandor_id' => null,
        ];

        if ($karyawanTipe === 'gudang') {
            $data['gudang_id'] = $karyawanId;
        } else {
            $data['mandor_id'] = $karyawanId;
        }

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
        
        // Determine employee type and ID
        $karyawanId = null;
        $tipeKaryawan = null;
        $namaKaryawan = null;
        
        if ($absensi->gudang_id) {
            $karyawanId = $absensi->gudang_id;
            $tipeKaryawan = 'gudang';
            $namaKaryawan = $absensi->gudang->nama;
        } elseif ($absensi->mandor_id) {
            $karyawanId = $absensi->mandor_id;
            $tipeKaryawan = 'mandor';
            $namaKaryawan = $absensi->mandor->nama;
        }
        
        if (!$karyawanId || !$tipeKaryawan) {
            return;
        }
        
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
        $absensis = Absensi::where($tipeKaryawan . '_id', $karyawanId)
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
            'karyawan_id' => 'required|string',
            'tanggal' => 'required|date',
            'status' => 'required|in:full,setengah_hari,absen',
        ]);

        // Parse karyawan_id format: "gudang_1" or "mandor_1"
        $karyawanParts = explode('_', $validated['karyawan_id']);
        $karyawanTipe = $karyawanParts[0];
        $karyawanId = $karyawanParts[1];

        // Find existing record
        $query = Absensi::where('tanggal', $validated['tanggal']);
        
        if ($karyawanTipe === 'gudang') {
            $query->where('gudang_id', $karyawanId);
        } else {
            $query->where('mandor_id', $karyawanId);
        }

        $absensi = $query->first();

        if ($absensi) {
            // Update existing record
            $absensi->update(['status' => $validated['status']]);
            
            // Update monthly attendance report
            $this->updateMonthlyReport($absensi);
            
            return response()->json(['success' => true, 'message' => 'Data absensi berhasil diperbarui']);
        } else {
            // Create new record if not exists
            $data = [
                'tanggal' => $validated['tanggal'],
                'status' => $validated['status'],
                'gudang_id' => null,
                'mandor_id' => null,
            ];

            if ($karyawanTipe === 'gudang') {
                $data['gudang_id'] = $karyawanId;
            } else {
                $data['mandor_id'] = $karyawanId;
            }

            $absensi = Absensi::create($data);
            $this->updateMonthlyReport($absensi);
            
            return response()->json(['success' => true, 'message' => 'Data absensi berhasil ditambahkan']);
        }
    }

    /**
     * Get employees by type for AJAX dropdown
     */
    public function getEmployees(Request $request)
    {
        $tipe = $request->get('tipe');

        if ($tipe === 'gudang') {
            $employees = Gudang::select('id', 'nama')->get();
        } elseif ($tipe === 'mandor') {
            $employees = Mandor::select('id', 'nama')->get();
        } else {
            $employees = collect();
        }

        return response()->json($employees);
    }
}