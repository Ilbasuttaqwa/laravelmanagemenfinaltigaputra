<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MonthlyAttendanceReport;
use App\Models\Absensi;
use App\Models\Gudang;
use App\Models\Mandor;
use Carbon\Carbon;

class MonthlyAttendanceReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', Carbon::now()->year);
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tipe = $request->get('tipe', 'all');

        $query = MonthlyAttendanceReport::periode($tahun, $bulan);

        if ($tipe !== 'all') {
            $query->tipeKaryawan($tipe);
        }

        $reports = $query->orderBy('nama_karyawan')->get();

        // Get available years and months for filter
        $availableYears = MonthlyAttendanceReport::distinct()->pluck('tahun')->sort()->values();
        $availableMonths = collect([
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ]);

        return view('monthly-attendance-reports.index', compact(
            'reports', 'tahun', 'bulan', 'tipe', 'availableYears', 'availableMonths'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(MonthlyAttendanceReport $monthlyAttendanceReport)
    {
        $report = $monthlyAttendanceReport;
        
        // Get detailed attendance data for the month
        $absensis = Absensi::where($report->tipe_karyawan . '_id', $report->karyawan_id)
            ->whereYear('tanggal', $report->tahun)
            ->whereMonth('tanggal', $report->bulan)
            ->orderBy('tanggal')
            ->get();

        return view('monthly-attendance-reports.show', compact('report', 'absensis'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MonthlyAttendanceReport $monthlyAttendanceReport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MonthlyAttendanceReport $monthlyAttendanceReport)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MonthlyAttendanceReport $monthlyAttendanceReport)
    {
        //
    }

    /**
     * Generate monthly report for specific period
     */
    public function generate(Request $request)
    {
        $tahun = $request->get('tahun', Carbon::now()->year);
        $bulan = $request->get('bulan', Carbon::now()->month);

        // Run the artisan command to generate reports
        \Artisan::call('attendance:generate-monthly-report', [
            'tahun' => $tahun,
            'bulan' => $bulan
        ]);

        return redirect()->route('monthly-attendance-reports.index', [
            'tahun' => $tahun,
            'bulan' => $bulan
        ])->with('success', 'Laporan bulanan berhasil digenerate untuk ' . $this->getMonthName($bulan) . ' ' . $tahun);
    }

    /**
     * Export monthly report to PDF
     */
    public function export(Request $request)
    {
        $tahun = $request->get('tahun', Carbon::now()->year);
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tipe = $request->get('tipe', 'all');

        $query = MonthlyAttendanceReport::periode($tahun, $bulan);

        if ($tipe !== 'all') {
            $query->tipeKaryawan($tipe);
        }

        $reports = $query->orderBy('nama_karyawan')->get();

        // Here you can implement PDF export using libraries like dompdf or tcpdf
        // For now, we'll return a simple view
        return view('monthly-attendance-reports.export', compact(
            'reports', 'tahun', 'bulan', 'tipe'
        ));
    }

    private function getMonthName($bulan)
    {
        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return $bulanNames[$bulan] ?? 'Bulan Tidak Valid';
    }
}