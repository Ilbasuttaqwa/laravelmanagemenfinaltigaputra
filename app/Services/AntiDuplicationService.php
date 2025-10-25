<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Employee;
use App\Models\Gudang;
use App\Models\Mandor;
use App\Models\Lokasi;
use App\Models\Kandang;
use App\Models\Pembibitan;
use App\Models\SalaryReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AntiDuplicationService
{
    /**
     * Check for duplicate attendance
     */
    public function checkDuplicateAbsensi($namaKaryawan, $tanggal, $excludeId = null)
    {
        try {
            $query = Absensi::where('nama_karyawan', $namaKaryawan)
                ->whereDate('tanggal', $tanggal);
            
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            
            $duplicate = $query->first();
            
            if ($duplicate) {
                Log::warning('Duplicate attendance detected', [
                    'nama_karyawan' => $namaKaryawan,
                    'tanggal' => $tanggal,
                    'existing_id' => $duplicate->id
                ]);
                return [
                    'is_duplicate' => true,
                    'message' => "Data absensi untuk {$namaKaryawan} pada tanggal {$tanggal} sudah ada.",
                    'existing_record' => $duplicate
                ];
            }
            
            return ['is_duplicate' => false];
        } catch (\Exception $e) {
            Log::error('Error checking duplicate absensi: ' . $e->getMessage());
            return ['is_duplicate' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Check for duplicate employee name
     */
    public function checkDuplicateEmployee($nama, $excludeId = null)
    {
        try {
            $query = Employee::where('nama', $nama);
            
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            
            $duplicate = $query->first();
            
            if ($duplicate) {
                Log::warning('Duplicate employee name detected', [
                    'nama' => $nama,
                    'existing_id' => $duplicate->id
                ]);
                return [
                    'is_duplicate' => true,
                    'message' => "Nama karyawan '{$nama}' sudah ada.",
                    'existing_record' => $duplicate
                ];
            }
            
            return ['is_duplicate' => false];
        } catch (\Exception $e) {
            Log::error('Error checking duplicate employee: ' . $e->getMessage());
            return ['is_duplicate' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Check for duplicate gudang name
     */
    public function checkDuplicateGudang($nama, $excludeId = null)
    {
        try {
            $query = Gudang::where('nama', $nama);
            
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            
            $duplicate = $query->first();
            
            if ($duplicate) {
                Log::warning('Duplicate gudang name detected', [
                    'nama' => $nama,
                    'existing_id' => $duplicate->id
                ]);
                return [
                    'is_duplicate' => true,
                    'message' => "Nama karyawan gudang '{$nama}' sudah ada.",
                    'existing_record' => $duplicate
                ];
            }
            
            return ['is_duplicate' => false];
        } catch (\Exception $e) {
            Log::error('Error checking duplicate gudang: ' . $e->getMessage());
            return ['is_duplicate' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Check for duplicate mandor name
     */
    public function checkDuplicateMandor($nama, $excludeId = null)
    {
        try {
            $query = Mandor::where('nama', $nama);
            
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            
            $duplicate = $query->first();
            
            if ($duplicate) {
                Log::warning('Duplicate mandor name detected', [
                    'nama' => $nama,
                    'existing_id' => $duplicate->id
                ]);
                return [
                    'is_duplicate' => true,
                    'message' => "Nama mandor '{$nama}' sudah ada.",
                    'existing_record' => $duplicate
                ];
            }
            
            return ['is_duplicate' => false];
        } catch (\Exception $e) {
            Log::error('Error checking duplicate mandor: ' . $e->getMessage());
            return ['is_duplicate' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Check for duplicate location name
     */
    public function checkDuplicateLokasi($namaLokasi, $excludeId = null)
    {
        try {
            $query = Lokasi::where('nama_lokasi', $namaLokasi);
            
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            
            $duplicate = $query->first();
            
            if ($duplicate) {
                Log::warning('Duplicate lokasi name detected', [
                    'nama_lokasi' => $namaLokasi,
                    'existing_id' => $duplicate->id
                ]);
                return [
                    'is_duplicate' => true,
                    'message' => "Nama lokasi '{$namaLokasi}' sudah ada.",
                    'existing_record' => $duplicate
                ];
            }
            
            return ['is_duplicate' => false];
        } catch (\Exception $e) {
            Log::error('Error checking duplicate lokasi: ' . $e->getMessage());
            return ['is_duplicate' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Check for duplicate kandang name within same location
     */
    public function checkDuplicateKandang($namaKandang, $lokasiId, $excludeId = null)
    {
        try {
            $query = Kandang::where('nama_kandang', $namaKandang)
                ->where('lokasi_id', $lokasiId);
            
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            
            $duplicate = $query->first();
            
            if ($duplicate) {
                Log::warning('Duplicate kandang name detected', [
                    'nama_kandang' => $namaKandang,
                    'lokasi_id' => $lokasiId,
                    'existing_id' => $duplicate->id
                ]);
                return [
                    'is_duplicate' => true,
                    'message' => "Nama kandang '{$namaKandang}' sudah ada di lokasi ini.",
                    'existing_record' => $duplicate
                ];
            }
            
            return ['is_duplicate' => false];
        } catch (\Exception $e) {
            Log::error('Error checking duplicate kandang: ' . $e->getMessage());
            return ['is_duplicate' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Check for duplicate pembibitan title within same kandang
     */
    public function checkDuplicatePembibitan($judul, $kandangId, $excludeId = null)
    {
        try {
            $query = Pembibitan::where('judul', $judul)
                ->where('kandang_id', $kandangId);
            
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            
            $duplicate = $query->first();
            
            if ($duplicate) {
                Log::warning('Duplicate pembibitan title detected', [
                    'judul' => $judul,
                    'kandang_id' => $kandangId,
                    'existing_id' => $duplicate->id
                ]);
                return [
                    'is_duplicate' => true,
                    'message' => "Judul pembibitan '{$judul}' sudah ada di kandang ini.",
                    'existing_record' => $duplicate
                ];
            }
            
            return ['is_duplicate' => false];
        } catch (\Exception $e) {
            Log::error('Error checking duplicate pembibitan: ' . $e->getMessage());
            return ['is_duplicate' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Check for duplicate salary report
     */
    public function checkDuplicateSalaryReport($employeeId, $tahun, $bulan, $excludeId = null)
    {
        try {
            $query = SalaryReport::where('employee_id', $employeeId)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan);
            
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            
            $duplicate = $query->first();
            
            if ($duplicate) {
                Log::warning('Duplicate salary report detected', [
                    'employee_id' => $employeeId,
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'existing_id' => $duplicate->id
                ]);
                return [
                    'is_duplicate' => true,
                    'message' => "Laporan gaji untuk karyawan ini pada periode {$bulan}/{$tahun} sudah ada.",
                    'existing_record' => $duplicate
                ];
            }
            
            return ['is_duplicate' => false];
        } catch (\Exception $e) {
            Log::error('Error checking duplicate salary report: ' . $e->getMessage());
            return ['is_duplicate' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Find and report all duplicates in the system
     */
    public function findAllDuplicates()
    {
        try {
            $duplicates = [];
            
            // Check for duplicate employees
            $duplicateEmployees = Employee::select('nama', DB::raw('COUNT(*) as count'))
                ->groupBy('nama')
                ->having('count', '>', 1)
                ->get();
            
            if ($duplicateEmployees->count() > 0) {
                $duplicates['employees'] = $duplicateEmployees;
            }
            
            // Check for duplicate gudang
            $duplicateGudang = Gudang::select('nama', DB::raw('COUNT(*) as count'))
                ->groupBy('nama')
                ->having('count', '>', 1)
                ->get();
            
            if ($duplicateGudang->count() > 0) {
                $duplicates['gudang'] = $duplicateGudang;
            }
            
            // Check for duplicate mandor
            $duplicateMandor = Mandor::select('nama', DB::raw('COUNT(*) as count'))
                ->groupBy('nama')
                ->having('count', '>', 1)
                ->get();
            
            if ($duplicateMandor->count() > 0) {
                $duplicates['mandor'] = $duplicateMandor;
            }
            
            // Check for duplicate lokasi
            $duplicateLokasi = Lokasi::select('nama_lokasi', DB::raw('COUNT(*) as count'))
                ->groupBy('nama_lokasi')
                ->having('count', '>', 1)
                ->get();
            
            if ($duplicateLokasi->count() > 0) {
                $duplicates['lokasi'] = $duplicateLokasi;
            }
            
            // Check for duplicate kandang
            $duplicateKandang = Kandang::select('nama_kandang', 'lokasi_id', DB::raw('COUNT(*) as count'))
                ->groupBy('nama_kandang', 'lokasi_id')
                ->having('count', '>', 1)
                ->get();
            
            if ($duplicateKandang->count() > 0) {
                $duplicates['kandang'] = $duplicateKandang;
            }
            
            // Check for duplicate pembibitan
            $duplicatePembibitan = Pembibitan::select('judul', 'kandang_id', DB::raw('COUNT(*) as count'))
                ->groupBy('judul', 'kandang_id')
                ->having('count', '>', 1)
                ->get();
            
            if ($duplicatePembibitan->count() > 0) {
                $duplicates['pembibitan'] = $duplicatePembibitan;
            }
            
            // Check for duplicate absensi
            $duplicateAbsensi = Absensi::select('nama_karyawan', 'tanggal', DB::raw('COUNT(*) as count'))
                ->groupBy('nama_karyawan', 'tanggal')
                ->having('count', '>', 1)
                ->get();
            
            if ($duplicateAbsensi->count() > 0) {
                $duplicates['absensi'] = $duplicateAbsensi;
            }
            
            Log::info('Duplicate check completed', [
                'total_duplicate_types' => count($duplicates),
                'duplicates' => $duplicates
            ]);
            
            return $duplicates;
        } catch (\Exception $e) {
            Log::error('Error finding duplicates: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Clean up duplicates (keep the latest record)
     */
    public function cleanupDuplicates()
    {
        try {
            DB::beginTransaction();
            
            $cleaned = [];
            
            // Clean duplicate employees (keep latest)
            $duplicateEmployees = Employee::select('nama', DB::raw('COUNT(*) as count'))
                ->groupBy('nama')
                ->having('count', '>', 1)
                ->get();
            
            foreach ($duplicateEmployees as $duplicate) {
                $employees = Employee::where('nama', $duplicate->nama)
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                // Keep the first (latest) one, delete the rest
                $toDelete = $employees->skip(1);
                foreach ($toDelete as $employee) {
                    $employee->delete();
                    $cleaned['employees'][] = $employee->id;
                }
            }
            
            // Clean duplicate gudang (keep latest)
            $duplicateGudang = Gudang::select('nama', DB::raw('COUNT(*) as count'))
                ->groupBy('nama')
                ->having('count', '>', 1)
                ->get();
            
            foreach ($duplicateGudang as $duplicate) {
                $gudangs = Gudang::where('nama', $duplicate->nama)
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                $toDelete = $gudangs->skip(1);
                foreach ($toDelete as $gudang) {
                    $gudang->delete();
                    $cleaned['gudang'][] = $gudang->id;
                }
            }
            
            // Clean duplicate mandor (keep latest)
            $duplicateMandor = Mandor::select('nama', DB::raw('COUNT(*) as count'))
                ->groupBy('nama')
                ->having('count', '>', 1)
                ->get();
            
            foreach ($duplicateMandor as $duplicate) {
                $mandors = Mandor::where('nama', $duplicate->nama)
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                $toDelete = $mandors->skip(1);
                foreach ($toDelete as $mandor) {
                    $mandor->delete();
                    $cleaned['mandor'][] = $mandor->id;
                }
            }
            
            // Clean duplicate lokasi (keep latest)
            $duplicateLokasi = Lokasi::select('nama_lokasi', DB::raw('COUNT(*) as count'))
                ->groupBy('nama_lokasi')
                ->having('count', '>', 1)
                ->get();
            
            foreach ($duplicateLokasi as $duplicate) {
                $lokasis = Lokasi::where('nama_lokasi', $duplicate->nama_lokasi)
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                $toDelete = $lokasis->skip(1);
                foreach ($toDelete as $lokasi) {
                    $lokasi->delete();
                    $cleaned['lokasi'][] = $lokasi->id;
                }
            }
            
            // Clean duplicate kandang (keep latest)
            $duplicateKandang = Kandang::select('nama_kandang', 'lokasi_id', DB::raw('COUNT(*) as count'))
                ->groupBy('nama_kandang', 'lokasi_id')
                ->having('count', '>', 1)
                ->get();
            
            foreach ($duplicateKandang as $duplicate) {
                $kandangs = Kandang::where('nama_kandang', $duplicate->nama_kandang)
                    ->where('lokasi_id', $duplicate->lokasi_id)
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                $toDelete = $kandangs->skip(1);
                foreach ($toDelete as $kandang) {
                    $kandang->delete();
                    $cleaned['kandang'][] = $kandang->id;
                }
            }
            
            // Clean duplicate pembibitan (keep latest)
            $duplicatePembibitan = Pembibitan::select('judul', 'kandang_id', DB::raw('COUNT(*) as count'))
                ->groupBy('judul', 'kandang_id')
                ->having('count', '>', 1)
                ->get();
            
            foreach ($duplicatePembibitan as $duplicate) {
                $pembibitans = Pembibitan::where('judul', $duplicate->judul)
                    ->where('kandang_id', $duplicate->kandang_id)
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                $toDelete = $pembibitans->skip(1);
                foreach ($toDelete as $pembibitan) {
                    $pembibitan->delete();
                    $cleaned['pembibitan'][] = $pembibitan->id;
                }
            }
            
            // Clean duplicate absensi (keep latest)
            $duplicateAbsensi = Absensi::select('nama_karyawan', 'tanggal', DB::raw('COUNT(*) as count'))
                ->groupBy('nama_karyawan', 'tanggal')
                ->having('count', '>', 1)
                ->get();
            
            foreach ($duplicateAbsensi as $duplicate) {
                $absensis = Absensi::where('nama_karyawan', $duplicate->nama_karyawan)
                    ->whereDate('tanggal', $duplicate->tanggal)
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                $toDelete = $absensis->skip(1);
                foreach ($toDelete as $absensi) {
                    $absensi->delete();
                    $cleaned['absensi'][] = $absensi->id;
                }
            }
            
            DB::commit();
            
            Log::info('Duplicate cleanup completed', $cleaned);
            return $cleaned;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cleaning up duplicates: ' . $e->getMessage());
            throw $e;
        }
    }
}
