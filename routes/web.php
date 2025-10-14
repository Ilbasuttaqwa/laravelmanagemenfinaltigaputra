<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\MandorController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\PembibitanController;
use App\Http\Controllers\KandangController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\MonthlyAttendanceReportController;
use App\Http\Controllers\CalendarAttendanceController;
use App\Http\Controllers\SalaryReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard/Home route
Route::get('/home', function () {
    $user = auth()->user();
    if ($user->isManager()) {
        return redirect()->route('manager.absensis.index');
    } elseif ($user->isAdmin()) {
        return redirect()->route('admin.manage');
    }
    return redirect()->route('login');
})->middleware('auth')->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Manager Routes (Posisi Tertinggi - Full Access)
Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::resource('gudangs', GudangController::class);
    Route::resource('mandors', MandorController::class);
    Route::resource('employees', EmployeeController::class);
    Route::resource('absensis', AbsensiController::class);
    Route::get('absensis/get-employees', [AbsensiController::class, 'getEmployees'])->name('absensis.get-employees');
    Route::post('absensis/update-existing', [AbsensiController::class, 'updateExisting'])->name('absensis.update-existing');
    Route::resource('pembibitans', PembibitanController::class);
    Route::resource('kandangs', KandangController::class);
    Route::resource('lokasis', LokasiController::class);
    
    // Monthly Attendance Reports
    Route::get('monthly-attendance-reports', [MonthlyAttendanceReportController::class, 'index'])->name('monthly-attendance-reports.index');
    Route::get('monthly-attendance-reports/{monthlyAttendanceReport}', [MonthlyAttendanceReportController::class, 'show'])->name('monthly-attendance-reports.show');
    Route::post('monthly-attendance-reports/generate', [MonthlyAttendanceReportController::class, 'generate'])->name('monthly-attendance-reports.generate');
    Route::get('monthly-attendance-reports/export/pdf', [MonthlyAttendanceReportController::class, 'export'])->name('monthly-attendance-reports.export');
    
    // Calendar Attendance
    Route::resource('calendar-attendances', CalendarAttendanceController::class);
    Route::post('calendar-attendances/{calendarAttendance}/update-day', [CalendarAttendanceController::class, 'updateDay'])->name('calendar-attendances.update-day');
    Route::get('calendar-attendances/get-attendance-data', [CalendarAttendanceController::class, 'getAttendanceData'])->name('calendar-attendances.get-attendance-data');
    Route::get('calendar-attendances/get-employees', [CalendarAttendanceController::class, 'getEmployees'])->name('calendar-attendances.get-employees');
    
    // Salary Reports
    Route::get('salary-reports', [SalaryReportController::class, 'index'])->name('salary-reports.index');
    Route::get('salary-reports/export', [SalaryReportController::class, 'export'])->name('salary-reports.export');
    Route::post('salary-reports/generate', [SalaryReportController::class, 'generate'])->name('salary-reports.generate');
    Route::get('salary-reports/{salaryReport}', [SalaryReportController::class, 'show'])->name('salary-reports.show');
});

// Admin Routes (Akses Terbatas - Tidak bisa input/edit mandor dan karyawan mandor)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin hanya bisa lihat master gudang (read-only)
    Route::get('gudangs', [GudangController::class, 'index'])->name('gudangs.index');
    Route::get('gudangs/create', [GudangController::class, 'create'])->name('gudangs.create');
    Route::post('gudangs', [GudangController::class, 'store'])->name('gudangs.store');
    Route::get('gudangs/{gudang}', [GudangController::class, 'show'])->name('gudangs.show');
    Route::get('gudangs/{gudang}/edit', [GudangController::class, 'edit'])->name('gudangs.edit');
    Route::put('gudangs/{gudang}', [GudangController::class, 'update'])->name('gudangs.update');
    // Admin tidak bisa hapus gudang
    
    // Admin tidak bisa akses mandor sama sekali
    // Route::resource('mandors', MandorController::class)->except(['destroy']); // DIHAPUS
    
    // Admin hanya bisa lihat karyawan dengan role 'karyawan' (bukan mandor)
    Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::get('employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    // Admin tidak bisa hapus karyawan
    
    // Admin bisa akses absensi tapi hanya untuk karyawan (bukan mandor)
    Route::get('absensis', [AbsensiController::class, 'index'])->name('absensis.index');
    Route::get('absensis/create', [AbsensiController::class, 'create'])->name('absensis.create');
    Route::post('absensis', [AbsensiController::class, 'store'])->name('absensis.store');
    Route::get('absensis/{absensi}', [AbsensiController::class, 'show'])->name('absensis.show');
    Route::get('absensis/{absensi}/edit', [AbsensiController::class, 'edit'])->name('absensis.edit');
    Route::put('absensis/{absensi}', [AbsensiController::class, 'update'])->name('absensis.update');
    // Admin tidak bisa hapus absensi
    Route::get('absensis/get-employees', [AbsensiController::class, 'getEmployees'])->name('absensis.get-employees');
    
    Route::resource('pembibitans', PembibitanController::class)->except(['destroy']);
    Route::resource('kandangs', KandangController::class)->except(['destroy']);
    Route::resource('lokasis', LokasiController::class)->except(['destroy']);
    
    // Monthly Attendance Reports (Read Only)
    Route::get('monthly-attendance-reports', [MonthlyAttendanceReportController::class, 'index'])->name('monthly-attendance-reports.index');
    Route::get('monthly-attendance-reports/{monthlyAttendanceReport}', [MonthlyAttendanceReportController::class, 'show'])->name('monthly-attendance-reports.show');
    Route::get('monthly-attendance-reports/export/pdf', [MonthlyAttendanceReportController::class, 'export'])->name('monthly-attendance-reports.export');
    
    // Calendar Attendance (Read Only)
    Route::get('calendar-attendances', [CalendarAttendanceController::class, 'index'])->name('calendar-attendances.index');
    Route::get('calendar-attendances/{calendarAttendance}', [CalendarAttendanceController::class, 'show'])->name('calendar-attendances.show');
    
    // Salary Reports (Read Only)
    Route::get('salary-reports', [SalaryReportController::class, 'index'])->name('salary-reports.index');
    Route::get('salary-reports/export', [SalaryReportController::class, 'export'])->name('salary-reports.export');
    Route::post('salary-reports/generate', [SalaryReportController::class, 'generate'])->name('salary-reports.generate');
    Route::get('salary-reports/{salaryReport}', [SalaryReportController::class, 'show'])->name('salary-reports.show');
    
    // Manage Page for Admin
    Route::get('manage', function () {
        $employees = \App\Models\Employee::orderBy('nama')->paginate(10);
        return view('admin.manage', compact('employees'));
    })->name('manage');
});
