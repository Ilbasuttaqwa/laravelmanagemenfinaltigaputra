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
    Route::get('salary-reports/{salaryReport}', [SalaryReportController::class, 'show'])->name('salary-reports.show');
    Route::post('salary-reports/generate', [SalaryReportController::class, 'generate'])->name('salary-reports.generate');
    Route::get('salary-reports/export', [SalaryReportController::class, 'export'])->name('salary-reports.export');
});

// Admin Routes (Posisi Di Bawah Manager)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('gudangs', GudangController::class)->except(['destroy']);
    Route::resource('mandors', MandorController::class)->except(['destroy']);
    Route::resource('employees', EmployeeController::class)->except(['destroy']);
    Route::resource('absensis', AbsensiController::class)->except(['destroy']);
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
    Route::get('salary-reports/{salaryReport}', [SalaryReportController::class, 'show'])->name('salary-reports.show');
    Route::get('salary-reports/export', [SalaryReportController::class, 'export'])->name('salary-reports.export');
    
    // Manage Page for Admin
    Route::get('manage', function () {
        $employees = \App\Models\Employee::orderBy('nama')->paginate(10);
        return view('admin.manage', compact('employees'));
    })->name('manage');
});
