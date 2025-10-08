<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalaryReport;
use App\Models\Gudang;
use App\Models\Mandor;
use App\Models\Lokasi;
use App\Models\Kandang;
use App\Models\Pembibitan;
use App\Models\Absensi;
use Carbon\Carbon;

class SalaryReportController extends Controller
{
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

        $query = SalaryReport::periode($tahun, $bulan)
            ->tipeKaryawan($tipe)
            ->lokasi($lokasiId)
            ->kandang($kandangId)
            ->pembibitan($pembibitanId)
            ->tanggalRange($tanggalMulai, $tanggalSelesai);

        $reports = $query->orderBy('nama_karyawan')->get();

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

        return redirect()->route('manager.salary-reports.index', [
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

        // Get all gudang employees
        $gudangs = Gudang::all();

        // Get all mandor employees  
        $mandors = Mandor::all();

        // Generate reports for gudang employees
        foreach ($gudangs as $gudang) {
            $this->createSalaryReport($gudang, 'gudang', $tahun, $bulan, $lokasiId, $kandangId, $pembibitanId, $tanggalMulai, $tanggalSelesai);
        }

        // Generate reports for mandor employees
        foreach ($mandors as $mandor) {
            $this->createSalaryReport($mandor, 'mandor', $tahun, $bulan, $lokasiId, $kandangId, $pembibitanId, $tanggalMulai, $tanggalSelesai);
        }
    }

    private function createSalaryReport($employee, $tipe, $tahun, $bulan, $lokasiId, $kandangId, $pembibitanId, $tanggalMulai, $tanggalSelesai)
    {
        // Calculate working days from attendance
        $startDate = $tanggalMulai ?? Carbon::create($tahun, $bulan, 1);
        $endDate = $tanggalSelesai ?? Carbon::create($tahun, $bulan)->endOfMonth();

        $attendanceQuery = Absensi::where(function($query) use ($employee, $tipe) {
            if ($tipe === 'gudang') {
                $query->where('gudang_id', $employee->id);
            } else {
                $query->where('mandor_id', $employee->id);
            }
        })->whereBetween('tanggal', [$startDate, $endDate]);

        $attendances = $attendanceQuery->get();
        $jmlHariKerja = $attendances->where('status', 'full')->count() + 
                       ($attendances->where('status', 'setengah_hari')->count() * 0.5);

        // Calculate total salary
        $gajiPokok = $employee->gaji;
        $totalGaji = $gajiPokok * ($jmlHariKerja / 22); // Assuming 22 working days per month

        // Get related entities (simplified for now)
        $lokasi = null;
        $kandang = null;
        $pembibitan = null;

        SalaryReport::create([
            'gudang_id' => $tipe === 'gudang' ? $employee->id : null,
            'mandor_id' => $tipe === 'mandor' ? $employee->id : null,
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