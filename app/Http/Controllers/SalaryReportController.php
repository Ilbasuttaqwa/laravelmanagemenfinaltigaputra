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
        $tahun = $request->get('tahun', Carbon::now()->year);
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tipe = $request->get('tipe', 'all');
        $lokasiId = $request->get('lokasi_id');
        $kandangId = $request->get('kandang_id');
        $pembibitanId = $request->get('pembibitan_id');
        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalSelesai = $request->get('tanggal_selesai');

        // Cek apakah ada filter yang dipilih
        $hasFilter = $lokasiId || $kandangId || $pembibitanId || $tanggalMulai || $tanggalSelesai || ($tipe !== 'all');
        
        if (!$hasFilter) {
            // Jika tidak ada filter, tampilkan tabel kosong
            $reports = collect();
        } else {
            $query = SalaryReport::periode($tahun, $bulan)
                ->tipeKaryawan($tipe)
                ->tanggalRange($tanggalMulai, $tanggalSelesai);
                
            // Filter lokasi dan kandang berdasarkan pembibitan yang dipilih
            if ($pembibitanId) {
                // Jika pembibitan dipilih, filter berdasarkan pembibitan tersebut
                $query->where('pembibitan_id', $pembibitanId);
            } else {
                // Jika lokasi dipilih, cari pembibitan di lokasi tersebut
                if ($lokasiId) {
                    $pembibitansInLokasi = \App\Models\Pembibitan::where('lokasi_id', $lokasiId)->pluck('id');
                    if ($pembibitansInLokasi->isNotEmpty()) {
                        $query->whereIn('pembibitan_id', $pembibitansInLokasi);
                    } else {
                        // Jika tidak ada pembibitan di lokasi tersebut, tampilkan kosong
                        $query->where('id', 0); // Force empty result
                    }
                }
                
                // Jika kandang dipilih, cari pembibitan di kandang tersebut
                if ($kandangId) {
                    $pembibitansInKandang = \App\Models\Pembibitan::where('kandang_id', $kandangId)->pluck('id');
                    if ($pembibitansInKandang->isNotEmpty()) {
                        $query->whereIn('pembibitan_id', $pembibitansInKandang);
                    } else {
                        // Jika tidak ada pembibitan di kandang tersebut, tampilkan kosong
                        $query->where('id', 0); // Force empty result
                    }
                }
            }
            
            $reports = $query->orderBy('nama_karyawan')->get();
            
            // Admin can only see salary reports for karyawan (not mandor)
            if ($this->getCurrentUser()?->isAdmin()) {
                $reports = $reports->where('tipe_karyawan', 'karyawan');
            }
        }

        // Get filter options
        $lokasis = Lokasi::orderBy('nama_lokasi')->get();
        $kandangs = Kandang::orderBy('nama_kandang')->get();
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

    public function generate(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer|min:2020|max:2030',
            'bulan' => 'required|integer|min:1|max:12',
            'lokasi_id' => 'nullable|exists:lokasis,id',
            'kandang_id' => 'nullable|exists:kandangs,id',
            'pembibitan_id' => 'nullable|exists:pembibitans,id',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $lokasiId = $request->lokasi_id;
        $kandangId = $request->kandang_id;
        $pembibitanId = $request->pembibitan_id;
        $tanggalMulai = $request->tanggal_mulai ? Carbon::parse($request->tanggal_mulai) : null;
        $tanggalSelesai = $request->tanggal_selesai ? Carbon::parse($request->tanggal_selesai) : null;

        // Generate salary reports for all employees
        $this->generateSalaryReports($tahun, $bulan, $lokasiId, $kandangId, $pembibitanId, $tanggalMulai, $tanggalSelesai);

        return redirect()->route($this->getCurrentUser()?->isAdmin() ? 'admin.salary-reports.index' : 'manager.salary-reports.index', [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'lokasi_id' => $lokasiId,
            'kandang_id' => $kandangId,
            'pembibitan_id' => $pembibitanId,
            'tanggal_mulai' => $tanggalMulai?->format('Y-m-d'),
            'tanggal_selesai' => $tanggalSelesai?->format('Y-m-d'),
        ])->with('success', 'Laporan gaji berhasil dibuat!');
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

        // Calculate total salary
        $gajiPokok = $employee->gaji_pokok;
        $totalGaji = $gajiPokok * ($jmlHariKerja / 22); // Assuming 22 working days per month

        // Get related entities from employee relationships
        $lokasi = $employee->lokasi;
        $kandang = $employee->kandang;
        
        // Get pembibitan from employee's recent attendance records
        $pembibitan = null;
        $recentAttendance = Absensi::where('employee_id', $employee->id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc')
            ->first();
            
        if ($recentAttendance && $recentAttendance->pembibitan_id) {
            $pembibitan = \App\Models\Pembibitan::find($recentAttendance->pembibitan_id);
        }

        SalaryReport::create([
            'employee_id' => $employee->id,
            'lokasi_id' => $lokasi?->id,
            'kandang_id' => $kandang?->id,
            'pembibitan_id' => $pembibitan?->id,
            'nama_karyawan' => $employee->nama,
            'tipe_karyawan' => $tipe,
            'gaji_pokok' => $gajiPokok,
            'jml_hari_kerja' => $jmlHariKerja,
            'total_gaji' => $totalGaji,
            'tanggal_mulai' => $startDate,
            'tanggal_selesai' => $endDate,
            'tahun' => $tahun,
            'bulan' => $bulan,
        ]);
    }
}