<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RealtimeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Real-time API Routes
Route::middleware(['auth', 'role:manager,admin'])->group(function () {
    Route::get('/absensi/realtime', [RealtimeController::class, 'getAbsensiData']);
    Route::get('/employees/realtime', [RealtimeController::class, 'getEmployeeData']);
    Route::get('/master/realtime', [RealtimeController::class, 'getMasterData']);
    Route::get('/performance/metrics', [RealtimeController::class, 'getPerformanceMetrics']);
    Route::post('/cache/clear', [RealtimeController::class, 'clearCache']);
});
