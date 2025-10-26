<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Employee;
use App\Models\Gudang;
use App\Models\Mandor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AutoSyncGajiService
{
    /**
     * Auto-sync gaji untuk semua absensi yang terkait dengan employee
     * Ketika gaji di master data diubah, otomatis update semua absensi
     */
    public function syncEmployeeGaji($employeeId, $newGajiPokok, $periodeMulai = null, $periodeSelesai = null)
    {
        try {
            DB::beginTransaction();
            
            $employee = Employee::find($employeeId);
            if (!$employee) {
                throw new \Exception('Employee tidak ditemukan');
            }

            // Tentukan periode sync
            $startDate = $periodeMulai ? Carbon::parse($periodeMulai) : Carbon::now()->startOfMonth();
            $endDate = $periodeSelesai ? Carbon::parse($periodeSelesai) : Carbon::now()->endOfMonth();

            Log::info('Auto-sync gaji dimulai', [
                'employee_id' => $employeeId,
                'employee_name' => $employee->nama,
                'old_gaji' => $employee->gaji_pokok,
                'new_gaji' => $newGajiPokok,
                'periode_mulai' => $startDate->format('Y-m-d'),
                'periode_selesai' => $endDate->format('Y-m-d')
            ]);

            // Update semua absensi dalam periode yang ditentukan
            $absensis = Absensi::where('employee_id', $employeeId)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->get();

            $updatedCount = 0;
            foreach ($absensis as $absensi) {
                // Hitung gaji hari itu berdasarkan gaji baru
                $gajiHariItu = $this->calculateGajiHariItu($newGajiPokok, $absensi->status);
                
                $absensi->update([
                    'gaji_pokok_saat_itu' => $newGajiPokok,
                    'gaji_hari_itu' => $gajiHariItu
                ]);
                
                $updatedCount++;
                
                Log::info('Absensi updated dengan gaji baru', [
                    'absensi_id' => $absensi->id,
                    'tanggal' => $absensi->tanggal,
                    'status' => $absensi->status,
                    'gaji_pokok_saat_itu' => $newGajiPokok,
                    'gaji_hari_itu' => $gajiHariItu
                ]);
            }

            DB::commit();

            Log::info('Auto-sync gaji selesai', [
                'employee_id' => $employeeId,
                'updated_absensi_count' => $updatedCount
            ]);

            return [
                'success' => true,
                'updated_count' => $updatedCount,
                'message' => "Berhasil sync gaji untuk {$updatedCount} absensi"
            ];

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error auto-sync gaji: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Gagal sync gaji: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Auto-sync gaji untuk gudang employee
     */
    public function syncGudangGaji($gudangId, $newGaji, $periodeMulai = null, $periodeSelesai = null)
    {
        try {
            DB::beginTransaction();
            
            $gudang = Gudang::find($gudangId);
            if (!$gudang) {
                throw new \Exception('Gudang tidak ditemukan');
            }

            // Tentukan periode sync
            $startDate = $periodeMulai ? Carbon::parse($periodeMulai) : Carbon::now()->startOfMonth();
            $endDate = $periodeSelesai ? Carbon::parse($periodeSelesai) : Carbon::now()->endOfMonth();

            // Update semua absensi gudang dalam periode
            $absensis = Absensi::where('nama_karyawan', $gudang->nama)
                ->whereNull('employee_id') // Gudang employees have null employee_id
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->get();

            $updatedCount = 0;
            foreach ($absensis as $absensi) {
                $gajiHariItu = $this->calculateGajiHariItu($newGaji, $absensi->status);
                
                $absensi->update([
                    'gaji_pokok_saat_itu' => $newGaji,
                    'gaji_hari_itu' => $gajiHariItu
                ]);
                
                $updatedCount++;
            }

            DB::commit();

            return [
                'success' => true,
                'updated_count' => $updatedCount,
                'message' => "Berhasil sync gaji gudang untuk {$updatedCount} absensi"
            ];

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error auto-sync gaji gudang: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Gagal sync gaji gudang: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Auto-sync gaji untuk mandor
     */
    public function syncMandorGaji($mandorId, $newGaji, $periodeMulai = null, $periodeSelesai = null)
    {
        try {
            DB::beginTransaction();
            
            $mandor = Mandor::find($mandorId);
            if (!$mandor) {
                throw new \Exception('Mandor tidak ditemukan');
            }

            // Tentukan periode sync
            $startDate = $periodeMulai ? Carbon::parse($periodeMulai) : Carbon::now()->startOfMonth();
            $endDate = $periodeSelesai ? Carbon::parse($periodeSelesai) : Carbon::now()->endOfMonth();

            // Update semua absensi mandor dalam periode
            $absensis = Absensi::where('nama_karyawan', $mandor->nama)
                ->where('jabatan', 'mandor')
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->get();

            $updatedCount = 0;
            foreach ($absensis as $absensi) {
                $gajiHariItu = $this->calculateGajiHariItu($newGaji, $absensi->status);
                
                $absensi->update([
                    'gaji_pokok_saat_itu' => $newGaji,
                    'gaji_hari_itu' => $gajiHariItu
                ]);
                
                $updatedCount++;
            }

            DB::commit();

            return [
                'success' => true,
                'updated_count' => $updatedCount,
                'message' => "Berhasil sync gaji mandor untuk {$updatedCount} absensi"
            ];

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error auto-sync gaji mandor: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Gagal sync gaji mandor: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Hitung gaji hari itu berdasarkan status
     */
    private function calculateGajiHariItu($gajiPokok, $status)
    {
        $gajiPerHari = $gajiPokok / 30; // Asumsi 30 hari per bulan
        
        switch ($status) {
            case 'full':
                return $gajiPerHari;
            case 'setengah_hari':
                return $gajiPerHari * 0.5;
            default:
                return 0;
        }
    }

    /**
     * Sync semua gaji untuk periode tertentu
     */
    public function syncAllGajiForPeriod($periodeMulai, $periodeSelesai)
    {
        try {
            $startDate = Carbon::parse($periodeMulai);
            $endDate = Carbon::parse($periodeSelesai);

            $totalUpdated = 0;

            // Sync employee gaji
            $employees = Employee::all();
            foreach ($employees as $employee) {
                $result = $this->syncEmployeeGaji($employee->id, $employee->gaji_pokok, $periodeMulai, $periodeSelesai);
                if ($result['success']) {
                    $totalUpdated += $result['updated_count'];
                }
            }

            // Sync gudang gaji
            $gudangs = Gudang::all();
            foreach ($gudangs as $gudang) {
                $result = $this->syncGudangGaji($gudang->id, $gudang->gaji, $periodeMulai, $periodeSelesai);
                if ($result['success']) {
                    $totalUpdated += $result['updated_count'];
                }
            }

            // Sync mandor gaji
            $mandors = Mandor::all();
            foreach ($mandors as $mandor) {
                $result = $this->syncMandorGaji($mandor->id, $mandor->gaji, $periodeMulai, $periodeSelesai);
                if ($result['success']) {
                    $totalUpdated += $result['updated_count'];
                }
            }

            return [
                'success' => true,
                'total_updated' => $totalUpdated,
                'message' => "Berhasil sync semua gaji untuk periode {$periodeMulai} - {$periodeSelesai}"
            ];

        } catch (\Exception $e) {
            Log::error('Error sync all gaji: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Gagal sync semua gaji: ' . $e->getMessage()
            ];
        }
    }
}
