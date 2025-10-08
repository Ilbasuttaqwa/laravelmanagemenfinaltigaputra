<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CalendarAttendance;
use App\Models\Gudang;
use App\Models\Mandor;
use App\Models\Absensi;
use Carbon\Carbon;

class CalendarAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', Carbon::now()->year);
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tipe = $request->get('tipe', 'all');

        // Get all employees (realtime from master data)
        $gudangs = Gudang::all();
        $mandors = Mandor::all();

        // Create virtual attendance records for all employees
        $attendances = collect();

        // Add gudang employees
        if ($tipe === 'all' || $tipe === 'gudang') {
            foreach ($gudangs as $gudang) {
                $attendances->push((object) [
                    'id' => 'gudang_' . $gudang->id,
                    'gudang_id' => $gudang->id,
                    'mandor_id' => null,
                    'nama_karyawan' => $gudang->nama,
                    'tipe_karyawan' => 'gudang',
                    'gudang' => $gudang,
                    'mandor' => null,
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'attendance_data' => $this->getAttendanceDataForEmployee($gudang->id, 'gudang', $tahun, $bulan)
                ]);
            }
        }

        // Add mandor employees
        if ($tipe === 'all' || $tipe === 'mandor') {
            foreach ($mandors as $mandor) {
                $attendances->push((object) [
                    'id' => 'mandor_' . $mandor->id,
                    'gudang_id' => null,
                    'mandor_id' => $mandor->id,
                    'nama_karyawan' => $mandor->nama,
                    'tipe_karyawan' => 'mandor',
                    'gudang' => null,
                    'mandor' => $mandor,
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'attendance_data' => $this->getAttendanceDataForEmployee($mandor->id, 'mandor', $tahun, $bulan)
                ]);
            }
        }

        // Get available years and months for filter
        $availableYears = collect([Carbon::now()->year - 1, Carbon::now()->year, Carbon::now()->year + 1]);
        $availableMonths = collect([
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ]);

        return view('calendar-attendances.index', compact(
            'attendances', 'gudangs', 'mandors', 'tahun', 'bulan', 'tipe', 
            'availableYears', 'availableMonths'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $gudangs = Gudang::all();
        $mandors = Mandor::all();
        $tahun = Carbon::now()->year;
        $bulan = Carbon::now()->month;

        return view('calendar-attendances.create', compact('gudangs', 'mandors', 'tahun', 'bulan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'karyawan_tipe' => 'required|in:gudang,mandor',
            'karyawan_id' => 'required|integer',
            'tahun' => 'required|integer|min:2020|max:2030',
            'bulan' => 'required|integer|min:1|max:12',
        ]);

        $karyawanTipe = $request->karyawan_tipe;
        $karyawanId = $request->karyawan_id;
        $tahun = $request->tahun;
        $bulan = $request->bulan;

        // Check if attendance record already exists
        $existingAttendance = CalendarAttendance::where($karyawanTipe . '_id', $karyawanId)
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->first();

        if ($existingAttendance) {
            return redirect()->back()->with('error', 'Data absensi untuk karyawan ini pada periode tersebut sudah ada.');
        }

        // Create new attendance record
        $attendanceData = [];
        $daysInMonth = Carbon::create($tahun, $bulan)->daysInMonth;
        
        // Initialize all days as 'absen'
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $attendanceData[$day] = 'absen';
        }

        $calendarAttendance = new CalendarAttendance();
        $calendarAttendance->{$karyawanTipe . '_id'} = $karyawanId;
        $calendarAttendance->tahun = $tahun;
        $calendarAttendance->bulan = $bulan;
        $calendarAttendance->attendance_data = $attendanceData;
        $calendarAttendance->save();

        return redirect()->route('manager.calendar-attendances.index', [
            'tahun' => $tahun,
            'bulan' => $bulan
        ])->with('success', 'Data absensi berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CalendarAttendance $calendarAttendance)
    {
        $calendarAttendance->load(['gudang', 'mandor']);
        return view('calendar-attendances.show', compact('calendarAttendance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CalendarAttendance $calendarAttendance)
    {
        $calendarAttendance->load(['gudang', 'mandor']);
        return view('calendar-attendances.edit', compact('calendarAttendance'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CalendarAttendance $calendarAttendance)
    {
        $request->validate([
            'attendance_data' => 'required|array',
            'attendance_data.*' => 'in:aktif,setengah_hari,absen',
        ]);

        $calendarAttendance->attendance_data = $request->attendance_data;
        $calendarAttendance->save();

        return redirect()->route('manager.calendar-attendances.index', [
            'tahun' => $calendarAttendance->tahun,
            'bulan' => $calendarAttendance->bulan
        ])->with('success', 'Data absensi berhasil diperbarui.');
    }

    /**
     * Update single day attendance
     */
    public function updateDay(Request $request, CalendarAttendance $calendarAttendance)
    {
        $request->validate([
            'day' => 'required|integer|min:1|max:31',
            'status' => 'required|in:aktif,setengah_hari,absen',
        ]);

        $day = $request->day;
        $status = $request->status;

        $calendarAttendance->updateAttendanceStatus($day, $status);

        return response()->json([
            'success' => true,
            'message' => 'Status absensi berhasil diperbarui',
            'day' => $day,
            'status' => $status
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CalendarAttendance $calendarAttendance)
    {
        $calendarAttendance->delete();

        return redirect()->route('manager.calendar-attendances.index')->with('success', 'Data absensi berhasil dihapus.');
    }

    /**
     * Get attendance data for specific employee and month
     */
    public function getAttendanceData(Request $request)
    {
        $karyawanTipe = $request->get('karyawan_tipe');
        $karyawanId = $request->get('karyawan_id');
        $tahun = $request->get('tahun', Carbon::now()->year);
        $bulan = $request->get('bulan', Carbon::now()->month);

        $attendance = CalendarAttendance::where($karyawanTipe . '_id', $karyawanId)
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Data absensi tidak ditemukan'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $attendance->attendance_data,
            'statistics' => $attendance->getStatistics()
        ]);
    }

    /**
     * Get attendance data for specific employee from absensis table
     */
    private function getAttendanceDataForEmployee($employeeId, $tipe, $tahun, $bulan)
    {
        $startDate = Carbon::create($tahun, $bulan, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        $query = Absensi::whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        
        if ($tipe === 'gudang') {
            $query->where('gudang_id', $employeeId);
        } else {
            $query->where('mandor_id', $employeeId);
        }
        
        $absensis = $query->get();
        
        // Convert to array format for calendar
        $attendanceData = [];
        foreach ($absensis as $absensi) {
            $day = $absensi->tanggal->day;
            $attendanceData[$day] = $absensi->status;
        }
        
        return $attendanceData;
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