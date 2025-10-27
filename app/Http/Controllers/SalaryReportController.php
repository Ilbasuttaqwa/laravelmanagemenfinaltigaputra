<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalaryReport;
use App\Models\Employee;
use App\Models\Lokasi;
use App\Models\Kandang;
use App\Models\Pembibitan;
use App\Models\Absensi;
use App\Models\User;
use App\Services\AutoSyncService;
use Carbon\Carbon;

class SalaryReportController extends Controller
{
    /**
     * Get current authenticated user
     * @return User|null
     */
    private function getCurrentUser(): ?User
    {
        return auth()->user();
    }
    public function index(Request $request)
    {
        // Auto-sync logic (runs automatically, no manual intervention needed)
        AutoSyncService::syncSalaryReports();
        AutoSyncService::clearCacheIfNeeded();
        
        // Force generate if no reports exist for current month
        $tahun = $request->get('tahun', Carbon::now()->year);
        $bulan = $request->get('bulan', Carbon::now()->month);
        
        $existingReports = SalaryReport::where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->count();
            
        if ($existingReports == 0) {
            AutoSyncService::forceGenerateSalaryReports($tahun, $bulan);
        }
        $tipe = $request->get('tipe', 'all');
        $lokasiId = $request->get('lokasi_id');
        $kandangId = $request->get('kandang_id');
        $pembibitanId = $request->get('pembibitan_id');
        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalSelesai = $request->get('tanggal_selesai');

        // Selalu tampilkan data berdasarkan periode yang dipilih
        $query = SalaryReport::periode($tahun, $bulan)
            ->tipeKaryawan($tipe);
            
        // Admin tidak boleh melihat mandor
        if (auth()->user()->isAdmin()) {
            $query->where('tipe_karyawan', '!=', 'mandor');
        }
            
        // Filter tanggal mulai dan selesai
        if ($tanggalMulai) {
            $query->where('tanggal_mulai', '>=', Carbon::parse($tanggalMulai));
        }
        if ($tanggalSelesai) {
            $query->where('tanggal_selesai', '<=', Carbon::parse($tanggalSelesai));
        }
                
        // Filter berdasarkan pembibitan, lokasi, dan kandang
        if ($pembibitanId) {
            // Jika pembibitan dipilih, filter berdasarkan pembibitan tersebut
            $query->where('pembibitan_id', $pembibitanId);
        }
        
        if ($lokasiId) {
            // Filter berdasarkan lokasi (melalui pembibitan atau langsung)
            $query->where('lokasi_id', $lokasiId);
        }
        
        if ($kandangId) {
            // Filter berdasarkan kandang (melalui pembibitan atau langsung)
            $query->where('kandang_id', $kandangId);
        }
            
        $reports = $query->orderBy('nama_karyawan')->get();
        
        // Admin can only see salary reports for karyawan (not mandor)
        if ($this->getCurrentUser()?->isAdmin()) {
            $reports = $reports->where('tipe_karyawan', 'karyawan');
        }

        // Get filter options - hanya yang memiliki data laporan gaji
        $lokasis = Lokasi::whereIn('id', function($query) use ($tahun, $bulan) {
            $query->select('lokasi_id')
                  ->from('salary_reports')
                  ->where('tahun', $tahun)
                  ->where('bulan', $bulan)
                  ->whereNotNull('lokasi_id');
        })->orderBy('nama_lokasi')->get();
        
        $kandangs = Kandang::whereIn('id', function($query) use ($tahun, $bulan) {
            $query->select('kandang_id')
                  ->from('salary_reports')
                  ->where('tahun', $tahun)
                  ->where('bulan', $bulan)
                  ->whereNotNull('kandang_id');
        })->orderBy('nama_kandang')->get();
        
        // Tampilkan semua pembibitan yang tersedia
        $pembibitans = Pembibitan::orderBy('judul')->get();

        $availableMonths = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('salary-reports.index', compact(
            'reports', 'tahun', 'bulan', 'tipe', 'lokasiId', 'kandangId', 
            'pembibitanId', 'tanggalMulai', 'tanggalSelesai',
            'lokasis', 'kandangs', 'pembibitans', 'availableMonths'
        ));
    }

    public function show(SalaryReport $salaryReport)
    {
        // Admin cannot view salary reports for mandor employees
        if ($this->getCurrentUser()?->isAdmin() && $salaryReport->tipe_karyawan === 'mandor') {
            abort(403, 'Admin tidak dapat melihat laporan gaji karyawan mandor.');
        }

        return view('salary-reports.show', compact('salaryReport'));
    }


    public function export(Request $request)
    {
        $tahun = $request->get('tahun', Carbon::now()->year);
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tipe = $request->get('tipe', 'all');
        $lokasiId = $request->get('lokasi_id');
        $kandangId = $request->get('kandang_id');
        $pembibitanId = $request->get('pembibitan_id');
        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalSelesai = $request->get('tanggal_selesai');
        $reportType = $request->get('report_type', 'rinci'); // rinci, singkat, biaya_gaji

        $query = SalaryReport::periode($tahun, $bulan)
            ->tipeKaryawan($tipe)
            ->lokasi($lokasiId)
            ->kandang($kandangId)
            ->pembibitan($pembibitanId)
            ->tanggalRange($tanggalMulai, $tanggalSelesai);

        // Admin can only see salary reports for karyawan (not mandor)
        if ($this->getCurrentUser()?->isAdmin()) {
            $query->where('tipe_karyawan', 'karyawan');
        }

        $reports = $query->orderBy('nama_karyawan')->get();

        $availableMonths = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('salary-reports.export', compact(
            'reports', 'tahun', 'bulan', 'tipe', 'lokasiId', 'kandangId', 
            'pembibitanId', 'tanggalMulai', 'tanggalSelesai', 'reportType', 'availableMonths'
        ));
    }

    private function generateSalaryReports($tahun, $bulan, $lokasiId, $kandangId, $pembibitanId, $tanggalMulai, $tanggalSelesai)
    {
        // Clear existing reports for this period
        SalaryReport::periode($tahun, $bulan)->delete();

        // Get employees based on user role
        $query = Employee::query();
        
        // Admin can only generate reports for karyawan (not mandor)
        if ($this->getCurrentUser()?->isAdmin()) {
            $query->where('jabatan', 'karyawan');
        }
        
        $employees = $query->get();

        // Generate reports for employees
        foreach ($employees as $employee) {
            $this->createSalaryReport($employee, $employee->jabatan, $tahun, $bulan, $lokasiId, $kandangId, $pembibitanId, $tanggalMulai, $tanggalSelesai);
        }

        // Generate reports for gudang employees (karyawan_gudang)
        if ($this->getCurrentUser()?->isManager()) {
            $gudangs = \App\Models\Gudang::all();
            foreach ($gudangs as $gudang) {
                $this->createSalaryReportForGudang($gudang, $tahun, $bulan, $lokasiId, $kandangId, $pembibitanId, $tanggalMulai, $tanggalSelesai);
            }
        }

        // Generate reports for mandor employees
        if ($this->getCurrentUser()?->isManager()) {
            $mandors = \App\Models\Mandor::all();
            foreach ($mandors as $mandor) {
                $this->createSalaryReportForMandor($mandor, $tahun, $bulan, $lokasiId, $kandangId, $pembibitanId, $tanggalMulai, $tanggalSelesai);
            }
        }
    }

    private function createSalaryReport($employee, $tipe, $tahun, $bulan, $lokasiId, $kandangId, $pembibitanId, $tanggalMulai, $tanggalSelesai)
    {
        // Calculate working days from attendance
        $startDate = $tanggalMulai ?? Carbon::create($tahun, $bulan, 1);
        $endDate = $tanggalSelesai ?? Carbon::create($tahun, $bulan)->endOfMonth();

        $attendanceQuery = Absensi::where('employee_id', $employee->id)
            ->whereBetween('tanggal', [$startDate, $endDate]);

        $attendances = $attendanceQuery->get();
        $jmlHariKerja = $attendances->where('status', 'full')->count() + 
                       ($attendances->where('status', 'setengah_hari')->count() * 0.5);

        // Calculate salary components
        $gajiPokokBulanan = $employee->gaji_pokok; // Gaji pokok bulanan dari master data
        
        // LOGIKA YANG BENAR: Gaji harian tetap (bukan dibagi hari kerja dalam bulan)
        // Jika gaji bulanan Rp 1.800.000, maka gaji harian = Rp 60.000 (full day)
        // Setengah hari = Rp 30.000
        $gajiHarianFull = $gajiPokokBulanan / 30; // Gaji harian full day (30 hari per bulan)
        $gajiHarianSetengah = $gajiHarianFull / 2; // Gaji harian setengah hari
        
        // Hitung gaji saat ini berdasarkan status absensi
        $gajiSaatIni = 0;
        foreach ($attendances as $attendance) {
            if ($attendance->status === 'full') {
                $gajiSaatIni += $gajiHarianFull;
            } elseif ($attendance->status === 'setengah_hari') {
                $gajiSaatIni += $gajiHarianSetengah;
            }
        }
        
        $totalGaji = $gajiSaatIni; // Total gaji = gaji saat ini

        // Get pembibitan from employee's recent attendance records
        $pembibitan = null;
        $lokasi = null;
        $kandang = null;
        
        $recentAttendance = Absensi::where('employee_id', $employee->id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc')
            ->first();
            
        if ($recentAttendance && $recentAttendance->pembibitan_id) {
            $pembibitan = \App\Models\Pembibitan::find($recentAttendance->pembibitan_id);
            
            // SELALU ambil lokasi dan kandang dari pembibitan jika ada
            if ($pembibitan) {
                $lokasi = $pembibitan->lokasi;
                $kandang = $pembibitan->kandang;
            }
        }
        
        // Fallback: jika tidak ada pembibitan atau pembibitan tidak memiliki lokasi/kandang
        if (!$lokasi && $employee->kandang && $employee->kandang->lokasi) {
            $lokasi = $employee->kandang->lokasi;
        }
        if (!$kandang && $employee->kandang) {
            $kandang = $employee->kandang;
        }


        SalaryReport::create([
            'employee_id' => $employee->id,
            'lokasi_id' => $lokasi?->id,
            'kandang_id' => $kandang?->id,
            'pembibitan_id' => $pembibitan?->id,
            'nama_karyawan' => $employee->nama,
            'tipe_karyawan' => $tipe,
            'gaji_pokok' => $gajiSaatIni, // Gaji saat ini (berdasarkan hari kerja)
            'gaji_pokok_bulanan' => $gajiPokokBulanan, // Gaji pokok bulanan dari master data
            'jml_hari_kerja' => $jmlHariKerja,
            'total_gaji' => $totalGaji,
            'tanggal_mulai' => $startDate,
            'tanggal_selesai' => $endDate,
            'tahun' => $tahun,
            'bulan' => $bulan,
        ]);
    }

    private function createSalaryReportForGudang($gudang, $tahun, $bulan, $lokasiId, $kandangId, $pembibitanId, $tanggalMulai, $tanggalSelesai)
    {
        // Calculate working days from attendance
        $startDate = $tanggalMulai ?? Carbon::create($tahun, $bulan, 1);
        $endDate = $tanggalSelesai ?? Carbon::create($tahun, $bulan)->endOfMonth();

        $attendanceQuery = Absensi::where('nama_karyawan', $gudang->nama)
            ->whereBetween('tanggal', [$startDate, $endDate]);

        $attendances = $attendanceQuery->get();
        $jmlHariKerja = $attendances->where('status', 'full')->count() + 
                       ($attendances->where('status', 'setengah_hari')->count() * 0.5);

        // Calculate salary components
        $gajiPokokBulanan = $gudang->gaji; // Gaji pokok bulanan dari master data gudang
        
        // LOGIKA YANG BENAR: Gaji harian tetap (bukan dibagi hari kerja dalam bulan)
        $gajiHarianFull = $gajiPokokBulanan / 30; // Gaji harian full day (30 hari per bulan)
        $gajiHarianSetengah = $gajiHarianFull / 2; // Gaji harian setengah hari
        
        // Hitung gaji saat ini berdasarkan status absensi
        $gajiSaatIni = 0;
        foreach ($attendances as $attendance) {
            if ($attendance->status === 'full') {
                $gajiSaatIni += $gajiHarianFull;
            } elseif ($attendance->status === 'setengah_hari') {
                $gajiSaatIni += $gajiHarianSetengah;
            }
        }
        
        $totalGaji = $gajiSaatIni; // Total gaji = gaji saat ini

        // Get pembibitan from gudang's recent attendance records
        $pembibitan = null;
        $lokasi = null;
        $kandang = null;
        
        $recentAttendance = Absensi::where('nama_karyawan', $gudang->nama)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc')
            ->first();
            
        if ($recentAttendance && $recentAttendance->pembibitan_id) {
            $pembibitan = \App\Models\Pembibitan::find($recentAttendance->pembibitan_id);
            
            // SELALU ambil lokasi dan kandang dari pembibitan jika ada
            if ($pembibitan) {
                $lokasi = $pembibitan->lokasi;
                $kandang = $pembibitan->kandang;
            }
        }

        SalaryReport::create([
            'employee_id' => null, // Gudang tidak ada employee_id
            'lokasi_id' => $lokasi?->id,
            'kandang_id' => $kandang?->id,
            'pembibitan_id' => $pembibitan?->id,
            'nama_karyawan' => $gudang->nama,
            'tipe_karyawan' => 'karyawan_gudang',
            'gaji_pokok' => $gajiSaatIni, // Gaji saat ini (berdasarkan hari kerja)
            'gaji_pokok_bulanan' => $gajiPokokBulanan, // Gaji pokok bulanan dari master data
            'jml_hari_kerja' => $jmlHariKerja,
            'total_gaji' => $totalGaji,
            'tanggal_mulai' => $startDate,
            'tanggal_selesai' => $endDate,
            'tahun' => $tahun,
            'bulan' => $bulan,
        ]);
    }

    private function createSalaryReportForMandor($mandor, $tahun, $bulan, $lokasiId, $kandangId, $pembibitanId, $tanggalMulai, $tanggalSelesai)
    {
        // Calculate working days from attendance
        $startDate = $tanggalMulai ?? Carbon::create($tahun, $bulan, 1);
        $endDate = $tanggalSelesai ?? Carbon::create($tahun, $bulan)->endOfMonth();

        $attendanceQuery = Absensi::where('nama_karyawan', $mandor->nama)
            ->whereBetween('tanggal', [$startDate, $endDate]);

        $attendances = $attendanceQuery->get();
        $jmlHariKerja = $attendances->where('status', 'full')->count() + 
                       ($attendances->where('status', 'setengah_hari')->count() * 0.5);

        // Calculate salary components
        $gajiPokokBulanan = $mandor->gaji; // Gaji pokok bulanan dari master data mandor
        
        // LOGIKA YANG BENAR: Gaji harian tetap (bukan dibagi hari kerja dalam bulan)
        $gajiHarianFull = $gajiPokokBulanan / 30; // Gaji harian full day (30 hari per bulan)
        $gajiHarianSetengah = $gajiHarianFull / 2; // Gaji harian setengah hari
        
        // Hitung gaji saat ini berdasarkan status absensi
        $gajiSaatIni = 0;
        foreach ($attendances as $attendance) {
            if ($attendance->status === 'full') {
                $gajiSaatIni += $gajiHarianFull;
            } elseif ($attendance->status === 'setengah_hari') {
                $gajiSaatIni += $gajiHarianSetengah;
            }
        }
        
        $totalGaji = $gajiSaatIni; // Total gaji = gaji saat ini

        // Get pembibitan from mandor's recent attendance records
        $pembibitan = null;
        $lokasi = null;
        $kandang = null;
        
        $recentAttendance = Absensi::where('nama_karyawan', $mandor->nama)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc')
            ->first();
            
        if ($recentAttendance && $recentAttendance->pembibitan_id) {
            $pembibitan = \App\Models\Pembibitan::find($recentAttendance->pembibitan_id);
            
            // SELALU ambil lokasi dan kandang dari pembibitan jika ada
            if ($pembibitan) {
                $lokasi = $pembibitan->lokasi;
                $kandang = $pembibitan->kandang;
            }
        }

        SalaryReport::create([
            'employee_id' => null, // Mandor tidak ada employee_id
            'lokasi_id' => $lokasi?->id,
            'kandang_id' => $kandang?->id,
            'pembibitan_id' => $pembibitan?->id,
            'nama_karyawan' => $mandor->nama,
            'tipe_karyawan' => 'mandor',
            'gaji_pokok' => $gajiSaatIni, // Gaji saat ini (berdasarkan hari kerja)
            'gaji_pokok_bulanan' => $gajiPokokBulanan, // Gaji pokok bulanan dari master data
            'jml_hari_kerja' => $jmlHariKerja,
            'total_gaji' => $totalGaji,
            'tanggal_mulai' => $startDate,
            'tanggal_selesai' => $endDate,
            'tahun' => $tahun,
            'bulan' => $bulan,
        ]);
    }
}