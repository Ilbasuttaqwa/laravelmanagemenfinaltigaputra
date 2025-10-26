<?php

namespace App\Http\Controllers;

use App\Services\AutoSyncGajiService;
use App\Models\Employee;
use App\Models\Gudang;
use App\Models\Mandor;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SyncGajiController extends Controller
{
    protected $autoSyncService;

    public function __construct()
    {
        $this->autoSyncService = new AutoSyncGajiService();
    }

    /**
     * Sync gaji untuk employee tertentu
     */
    public function syncEmployee(Request $request, Employee $employee)
    {
        $request->validate([
            'periode_mulai' => 'nullable|date',
            'periode_selesai' => 'nullable|date|after_or_equal:periode_mulai',
        ]);

        $periodeMulai = $request->periode_mulai ?: Carbon::now()->startOfMonth()->format('Y-m-d');
        $periodeSelesai = $request->periode_selesai ?: Carbon::now()->endOfMonth()->format('Y-m-d');

        $result = $this->autoSyncService->syncEmployeeGaji(
            $employee->id,
            $employee->gaji_pokok,
            $periodeMulai,
            $periodeSelesai
        );

        if ($result['success']) {
            return redirect()->back()
                ->with('success', "✅ Gaji {$employee->nama} berhasil disinkronkan untuk {$result['updated_count']} absensi pada periode {$periodeMulai} - {$periodeSelesai}");
        } else {
            return redirect()->back()
                ->with('error', "❌ Gagal sinkronisasi gaji: {$result['message']}");
        }
    }

    /**
     * Sync gaji untuk gudang tertentu
     */
    public function syncGudang(Request $request, Gudang $gudang)
    {
        $request->validate([
            'periode_mulai' => 'nullable|date',
            'periode_selesai' => 'nullable|date|after_or_equal:periode_mulai',
        ]);

        $periodeMulai = $request->periode_mulai ?: Carbon::now()->startOfMonth()->format('Y-m-d');
        $periodeSelesai = $request->periode_selesai ?: Carbon::now()->endOfMonth()->format('Y-m-d');

        $result = $this->autoSyncService->syncGudangGaji(
            $gudang->id,
            $gudang->gaji,
            $periodeMulai,
            $periodeSelesai
        );

        if ($result['success']) {
            return redirect()->back()
                ->with('success', "✅ Gaji {$gudang->nama} berhasil disinkronkan untuk {$result['updated_count']} absensi pada periode {$periodeMulai} - {$periodeSelesai}");
        } else {
            return redirect()->back()
                ->with('error', "❌ Gagal sinkronisasi gaji: {$result['message']}");
        }
    }

    /**
     * Sync gaji untuk mandor tertentu
     */
    public function syncMandor(Request $request, Mandor $mandor)
    {
        $request->validate([
            'periode_mulai' => 'nullable|date',
            'periode_selesai' => 'nullable|date|after_or_equal:periode_mulai',
        ]);

        $periodeMulai = $request->periode_mulai ?: Carbon::now()->startOfMonth()->format('Y-m-d');
        $periodeSelesai = $request->periode_selesai ?: Carbon::now()->endOfMonth()->format('Y-m-d');

        $result = $this->autoSyncService->syncMandorGaji(
            $mandor->id,
            $mandor->gaji,
            $periodeMulai,
            $periodeSelesai
        );

        if ($result['success']) {
            return redirect()->back()
                ->with('success', "✅ Gaji {$mandor->nama} berhasil disinkronkan untuk {$result['updated_count']} absensi pada periode {$periodeMulai} - {$periodeSelesai}");
        } else {
            return redirect()->back()
                ->with('error', "❌ Gagal sinkronisasi gaji: {$result['message']}");
        }
    }

    /**
     * Sync semua gaji untuk periode tertentu
     */
    public function syncAll(Request $request)
    {
        $request->validate([
            'periode_mulai' => 'nullable|date',
            'periode_selesai' => 'nullable|date|after_or_equal:periode_mulai',
        ]);

        $periodeMulai = $request->periode_mulai ?: Carbon::now()->startOfMonth()->format('Y-m-d');
        $periodeSelesai = $request->periode_selesai ?: Carbon::now()->endOfMonth()->format('Y-m-d');

        $result = $this->autoSyncService->syncAllGajiForPeriod(
            $periodeMulai,
            $periodeSelesai
        );

        if ($result['success']) {
            return redirect()->back()
                ->with('success', "✅ Semua gaji berhasil disinkronkan untuk {$result['total_updated']} absensi pada periode {$periodeMulai} - {$periodeSelesai}");
        } else {
            return redirect()->back()
                ->with('error', "❌ Gagal sinkronisasi semua gaji: {$result['message']}");
        }
    }
}
