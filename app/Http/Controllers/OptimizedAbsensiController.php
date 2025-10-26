<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Employee;
use App\Models\Pembibitan;
use App\Models\User;
use App\Services\SmartCacheService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class OptimizedAbsensiController extends Controller
{
    /**
     * Optimized create method with smart caching
     */
    public function create()
    {
        // Use smart cache instead of clearing all cache
        $employees = SmartCacheService::getEmployees();
        $gudangEmployees = collect();
        $mandorEmployees = collect();
        
        if ($this->getCurrentUser() && method_exists($this->getCurrentUser(), 'isManager') && $this->getCurrentUser()->isManager()) {
            $gudangEmployees = SmartCacheService::getGudangs()->map(function($gudang) {
                return (object) [
                    'id' => 'gudang_' . $gudang->id,
                    'nama' => $gudang->nama,
                    'jabatan' => 'karyawan_gudang',
                    'gaji_pokok' => $gudang->gaji,
                    'source' => 'gudang'
                ];
            });
            
            $mandorEmployees = SmartCacheService::getMandors()->map(function($mandor) {
                return (object) [
                    'id' => 'mandor_' . $mandor->id,
                    'nama' => $mandor->nama,
                    'jabatan' => 'mandor',
                    'gaji_pokok' => $mandor->gaji,
                    'source' => 'mandor'
                ];
            });
        }
        
        // Combine all employees with optimized processing
        $allEmployees = $gudangEmployees
            ->concat($employees->map(function($employee) {
                return (object) [
                    'id' => 'employee_' . $employee->id,
                    'nama' => $employee->nama,
                    'jabatan' => $employee->jabatan,
                    'gaji_pokok' => $employee->gaji_pokok,
                    'source' => 'employee'
                ];
            }))
            ->concat($mandorEmployees)
            ->unique('nama')
            ->sortBy('nama');
        
        $pembibitans = SmartCacheService::getPembibitans();
        
        return view('absensis.create', compact('allEmployees', 'pembibitans'));
    }
    
    /**
     * Optimized index method with pagination and smart queries
     */
    public function index(Request $request)
    {
        // Use smart cache for filter options
        $pembibitans = SmartCacheService::getPembibitans();
        
        // Build query with optimized conditions
        $query = Absensi::with(['employee', 'pembibitan'])
            ->select('id', 'employee_id', 'nama_karyawan', 'tanggal', 'status', 
                    'gaji_hari_itu', 'lokasi_kerja', 'pembibitan_id', 'created_at');
        
        // Admin can only see absensi for karyawan (not mandor)
        if ($this->getCurrentUser() && method_exists($this->getCurrentUser(), 'isAdmin') && $this->getCurrentUser()->isAdmin()) {
            $query->whereHas('employee', function($employeeQuery) {
                $employeeQuery->whereIn('jabatan', ['karyawan', 'karyawan_gudang']);
            });
        }
        
        // Apply filters with optimized queries
        $hasFilter = false;
        
        if ($request->filled('lokasi_filter')) {
            $query->where('lokasi_kerja', 'like', '%' . $request->lokasi_filter . '%');
            $hasFilter = true;
        }
        
        if ($request->filled('kandang_filter')) {
            $query->where('lokasi_kerja', 'like', '%' . $request->kandang_filter . '%');
            $hasFilter = true;
        }
        
        if ($request->filled('tanggal_filter')) {
            $query->whereDate('tanggal', $request->tanggal_filter);
            $hasFilter = true;
        }
        
        if ($request->filled('bibit_filter')) {
            $bibitFilter = $request->bibit_filter;
            $query->where(function($q) use ($bibitFilter) {
                $q->whereHas('pembibitan', function($pembibitanQuery) use ($bibitFilter) {
                    $pembibitanQuery->where('judul', 'like', '%' . $bibitFilter . '%');
                })
                ->orWhere('lokasi_kerja', 'like', '%' . $bibitFilter . '%')
                ->orWhere('nama_karyawan', 'like', '%' . $bibitFilter . '%');
            });
            $hasFilter = true;
        }
        
        // If no filter, show empty table
        if (!$hasFilter) {
            $query->whereRaw('1 = 0');
        }
        
        // Use DataTables with optimized settings
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('nama_karyawan', function($absensi) {
                // Always get fresh employee data first
                if ($absensi->employee_id) {
                    $employee = Employee::find($absensi->employee_id);
                    if ($employee) {
                        return $employee->nama;
                    }
                }
                
                // For gudang/mandor employees, use stored nama_karyawan
                if (!empty($absensi->nama_karyawan)) {
                    return $absensi->nama_karyawan;
                }
                
                return 'Karyawan Tidak Ditemukan';
            })
            ->addColumn('role_karyawan', function($absensi) {
                // Get role from employee relationship safely
                if ($absensi->employee && $absensi->employee->jabatan) {
                    $jabatan = $absensi->employee->jabatan;
                    // Transform role names for display
                    return match($jabatan) {
                        'karyawan' => 'karyawan kandang',
                        'karyawan_gudang' => 'karyawan gudang',
                        'mandor' => 'mandor',
                        default => $jabatan
                    };
                }
                return 'karyawan kandang'; // Default fallback
            })
            ->addColumn('lokasi_kerja', function($absensi) {
                if ($absensi->pembibitan && $absensi->pembibitan->lokasi) {
                    return $absensi->pembibitan->lokasi->nama_lokasi;
                }
                return $absensi->lokasi_kerja ?? 'Kantor Pusat';
            })
            ->addColumn('pembibitan_info', function($absensi) {
                if ($absensi->pembibitan) {
                    return $absensi->pembibitan->judul;
                }
                return '-';
            })
            ->addColumn('gaji_formatted', function($absensi) {
                return 'Rp ' . number_format($absensi->gaji_hari_itu, 0, ',', '.');
            })
            ->addColumn('status_badge', function($absensi) {
                $badgeClass = $absensi->status === 'full' ? 'success' : 'warning';
                $statusText = $absensi->status === 'full' ? 'Full Day' : '1/2 Hari';
                return '<span class="badge bg-' . $badgeClass . '">' . $statusText . '</span>';
            })
            ->addColumn('action', function($absensi) {
                /** @var \App\Models\User|null $user */
                $user = auth()->user();
                $isManager = $user && $user->isManager();
                $editUrl = route($isManager ? 'manager.absensis.edit' : 'admin.absensis.edit', $absensi->id);
                $deleteUrl = route($isManager ? 'manager.absensis.destroy' : 'admin.absensis.destroy', $absensi->id);
                
                return '
                    <div class="btn-group" role="group">
                        <a href="' . $editUrl . '" class="btn btn-sm btn-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="' . $deleteUrl . '" style="display: inline;">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-danger" 
                                    onclick="return confirm(\'Yakin ingin menghapus?\')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                ';
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }
    
    /**
     * Optimized bulk store with batch processing
     */
    public function bulkStore(Request $request)
    {
        try {
            $request->validate([
                'tanggal' => 'required|date',
                'employees' => 'required|array',
                'employees.*.id' => 'required|string',
                'employees.*.status' => 'required|in:full,setengah_hari',
                'employees.*.pembibitan_id' => 'nullable|exists:pembibitans,id'
            ]);

            $tanggal = $request->tanggal;
            $employees = $request->employees;
            $successCount = 0;
            $errors = [];
            
            // Process in batches of 50 to avoid memory issues
            $batches = array_chunk($employees, 50);
            
            foreach ($batches as $batch) {
                $batchResults = $this->processBatch($batch, $tanggal);
                $successCount += $batchResults['success'];
                $errors = array_merge($errors, $batchResults['errors']);
                
                // Clear memory after each batch
                if (function_exists('gc_collect_cycles')) {
                    gc_collect_cycles();
                }
            }

            $message = "Berhasil menyimpan {$successCount} absensi";
            if (!empty($errors)) {
                $message .= ". Error: " . implode(', ', array_slice($errors, 0, 5)); // Limit error messages
            }

            // Clear relevant caches after bulk operation
            SmartCacheService::smartClear('employees');

            return response()->json([
                'success' => $successCount > 0,
                'message' => $message,
                'success_count' => $successCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk store absensi error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan absensi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Process batch of employees
     */
    private function processBatch($batch, $tanggal)
    {
        $successCount = 0;
        $errors = [];
        
        foreach ($batch as $employeeData) {
            try {
                // Parse employee_id
                $employeeId = $employeeData['id'];
                $actualEmployeeId = null;
                $source = null;

                if (str_starts_with($employeeId, 'employee_')) {
                    $actualEmployeeId = str_replace('employee_', '', $employeeId);
                    $source = 'employee';
                } elseif (str_starts_with($employeeId, 'gudang_')) {
                    $actualEmployeeId = str_replace('gudang_', '', $employeeId);
                    $source = 'gudang';
                } elseif (str_starts_with($employeeId, 'mandor_')) {
                    $actualEmployeeId = str_replace('mandor_', '', $employeeId);
                    $source = 'mandor';
                } else {
                    $actualEmployeeId = $employeeId;
                    $source = 'employee';
                }

                // Get employee data
                $employee = $this->getEmployeeData($source, $actualEmployeeId);
                
                if (!$employee) {
                    $errors[] = "Karyawan dengan ID {$employeeId} tidak ditemukan";
                    continue;
                }

                // Check for duplicate
                $existingAbsensi = $this->checkDuplicateAbsensi($source, $actualEmployeeId, $employee->nama, $tanggal);
                if ($existingAbsensi) {
                    $errors[] = "Absensi untuk {$employee->nama} pada tanggal {$tanggal} sudah ada";
                    continue;
                }

                // Create absensi record
                $absensiData = [
                    'tanggal' => $tanggal,
                    'status' => $employeeData['status'],
                    'nama_karyawan' => $employee->nama,
                    'gaji_pokok_saat_itu' => $employee->gaji_pokok,
                    'gaji_hari_itu' => $employee->gaji_pokok,
                    'lokasi_kerja' => $employee->lokasi_kerja ?? 'Kantor Pusat',
                    'pembibitan_id' => $employeeData['pembibitan_id'] ?? null,
                ];

                if ($source === 'employee') {
                    $absensiData['employee_id'] = $actualEmployeeId;
                    $absensiData['kandang_id'] = $employee->kandang_id ?? null;
                }

                Absensi::create($absensiData);
                $successCount++;

            } catch (\Exception $e) {
                $errors[] = "Error untuk {$employeeData['id']}: " . $e->getMessage();
            }
        }
        
        return ['success' => $successCount, 'errors' => $errors];
    }
    
    /**
     * Get employee data with caching
     */
    private function getEmployeeData($source, $id)
    {
        switch ($source) {
            case 'employee':
                return Employee::find($id);
            case 'gudang':
                return \App\Models\Gudang::find($id);
            case 'mandor':
                return \App\Models\Mandor::find($id);
            default:
                return null;
        }
    }
    
    /**
     * Check for duplicate absensi
     */
    private function checkDuplicateAbsensi($source, $id, $nama, $tanggal)
    {
        if ($source === 'employee') {
            return Absensi::where('employee_id', $id)
                ->whereDate('tanggal', $tanggal)
                ->first();
        } else {
            return Absensi::where('nama_karyawan', $nama)
                ->whereDate('tanggal', $tanggal)
                ->first();
        }
    }
    
    /**
     * Get current user
     */
    private function getCurrentUser(): ?User
    {
        return auth()->user();
    }
}
