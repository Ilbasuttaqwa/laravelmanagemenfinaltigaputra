<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Employee;
use App\Models\Gudang;
use App\Models\Kandang;
use App\Models\Lokasi;
use App\Models\Pembibitan;
use App\Http\Requests\StoreAbsensiRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\Facades\DataTables;

class AbsensiController extends Controller
{
    public function __construct()
    {
        // Constructor kosong untuk menghindari dependency injection issues
    }
    public function index(Request $request)
    {
        // Clear ALL caches untuk memastikan data fresh
        \Cache::flush();
        
        // Force fresh data - disable query caching
        $employees = Employee::where('jabatan', 'karyawan')
            ->with(['lokasi', 'kandang'])
            ->orderBy('nama')
            ->get();

        $lokasis = Lokasi::orderBy('nama_lokasi')->get();
        $kandangs = Kandang::with('lokasi')->orderBy('nama_kandang')->get();
        $pembibitans = Pembibitan::with(['lokasi', 'kandang'])->orderBy('judul')->get();

        if ($request->ajax()) {
            $query = Absensi::with(['employee']);
        
        // Admin can only see absensi for karyawan (not mandor)
        if (auth()->user()->isAdmin()) {
            $query->whereHas('employee', function($employeeQuery) {
                $employeeQuery->whereIn('jabatan', ['karyawan', 'karyawan_gudang']);
            });
        }
            
            // Apply filters - Hanya tampilkan data jika ada filter yang dipilih
            $hasFilter = false;
            
            if ($request->filled('lokasi_filter') && $request->lokasi_filter !== '') {
                $query->where('lokasi_kerja', 'like', '%' . $request->lokasi_filter . '%');
                $hasFilter = true;
            }
            
            if ($request->filled('kandang_filter') && $request->kandang_filter !== '') {
                $query->where('lokasi_kerja', 'like', '%' . $request->kandang_filter . '%');
                $hasFilter = true;
            }
            
            if ($request->filled('tanggal_filter') && $request->tanggal_filter !== '') {
                $query->whereDate('tanggal', $request->tanggal_filter);
                $hasFilter = true;
            }
            
            if ($request->filled('bibit_filter') && $request->bibit_filter !== '') {
                $bibitFilter = $request->bibit_filter;
                $query->where(function($q) use ($bibitFilter) {
                    // Search in pembibitan titles via direct relationship
                    $q->whereHas('pembibitan', function($pembibitanQuery) use ($bibitFilter) {
                        $pembibitanQuery->where('judul', 'like', '%' . $bibitFilter . '%');
                    })
                    // Search in lokasi kerja (auto-detect pembibitan)
                    ->orWhere('lokasi_kerja', 'like', '%' . $bibitFilter . '%')
                    // Search in employee names
                    ->orWhere('nama_karyawan', 'like', '%' . $bibitFilter . '%');
                });
                $hasFilter = true;
            }
            
            // Jika tidak ada filter yang dipilih, return empty result (kecuali untuk admin)
            if (!$hasFilter && !auth()->user()->isAdmin()) {
                $query->whereRaw('1 = 0'); // Force empty result
            }
            
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('nama_karyawan', function($absensi) {
                    // Prioritize stored data, fallback to employee relationship
                    if (!empty($absensi->nama_karyawan)) {
                        return $absensi->nama_karyawan;
                    }
                    
                    // Try to get from employee relationship
                    if ($absensi->employee) {
                        return $absensi->employee->nama;
                    }
                    
                    // Try to find employee by ID
                    $employee = Employee::find($absensi->employee_id);
                    if ($employee) {
                        return $employee->nama;
                    }
                    
                    return 'Karyawan Tidak Ditemukan';
                })
                ->addColumn('role_karyawan', function($absensi) {
                    // Get role from employee relationship
                    return $absensi->employee->jabatan ?? 'karyawan';
                })
                ->addColumn('status_badge', function($absensi) {
                    $badgeClass = match($absensi->status) {
                        'full' => 'success',
                        'setengah_hari' => 'warning',
                        default => 'secondary'
                    };
                    $statusText = match($absensi->status) {
                        'full' => 'Full Day',
                        'setengah_hari' => '½ Hari',
                        default => ucfirst($absensi->status)
                    };
                    return '<span class="badge bg-' . $badgeClass . '">' . $statusText . '</span>';
                })
                ->addColumn('tanggal_formatted', function($absensi) {
                    return $absensi->tanggal->format('d/m/Y');
                })
                ->addColumn('lokasi_kerja', function($absensi) {
                    // Always get fresh data from employee relationship
                    if ($absensi->employee_id) {
                        $employee = Employee::with('lokasi')->find($absensi->employee_id);
                        if ($employee && $employee->lokasi) {
                            return $employee->lokasi->nama_lokasi;
                        }
                    }
                    
                    // Fallback to stored data
                    return $absensi->lokasi_kerja ?? '-';
                })
                ->addColumn('pembibitan_info', function($absensi) {
                    // Cari pembibitan berdasarkan relasi langsung (sesuai ERD)
                    if ($absensi->pembibitan_id) {
                        $pembibitan = \App\Models\Pembibitan::find($absensi->pembibitan_id);
                        if ($pembibitan) {
                            return '<span class="badge bg-info">' . $pembibitan->judul . '</span>';
                        }
                    }
                    
                    // Auto-detect pembibitan berdasarkan lokasi kerja dan kandang employee
                    if ($absensi->employee && $absensi->employee->kandang_id) {
                        $pembibitan = \App\Models\Pembibitan::where('kandang_id', $absensi->employee->kandang_id)
                            ->where('lokasi_id', $absensi->employee->kandang->lokasi_id)
                            ->first();
                        
                        if ($pembibitan) {
                            return '<span class="badge bg-info">' . $pembibitan->judul . '</span>';
                        }
                    }
                    
                    return '<span class="text-muted">-</span>';
                })
                ->addColumn('action', function($absensi) {
                    $editUrl = auth()->user()->isAdmin() 
                        ? route('admin.absensis.edit', $absensi->id)
                        : route('manager.absensis.edit', $absensi->id);
                    
                    // Admin tidak bisa hapus absensi, hanya manager
                    if (auth()->user()->isManager()) {
                        $deleteUrl = route('manager.absensis.destroy', $absensi->id);
                        return '
                            <div class="btn-group" role="group">
                                <a href="' . $editUrl . '" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="' . $deleteUrl . '" style="display: inline;" onsubmit="return confirm(\'Yakin ingin menghapus?\')">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        ';
                    } else {
                        // Admin hanya bisa edit
                        return '
                            <div class="btn-group" role="group">
                                <a href="' . $editUrl . '" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        ';
                    }
                })
                ->rawColumns(['status_badge', 'pembibitan_info', 'action'])
                ->make(true);
        }
        
        // Force fresh data - clear all caches first
        \Cache::forget('lokasis_data');
        \Cache::forget('kandangs_data');
        \Cache::forget('pembibitans_data');
        \Cache::forget('gudangs_data');
        
        // Load master data for filters with fresh query
        $lokasis = Lokasi::orderBy('nama_lokasi')->get();
        $kandangs = Kandang::with('lokasi')->orderBy('nama_kandang')->get();
        $pembibitans = Pembibitan::with(['lokasi', 'kandang'])->orderBy('judul')->get();
        $gudangs = Gudang::orderBy('nama')->get();
        
        return view('absensis.index', compact('lokasis', 'kandangs', 'pembibitans', 'gudangs', 'employees'));
    }

    /**
     * Refresh master data for real-time updates
     */
    public function refreshMasterData()
    {
        // Clear all caches
        \Cache::forget('lokasis_data');
        \Cache::forget('kandangs_data');
        \Cache::forget('pembibitans_data');
        \Cache::forget('gudangs_data');
        \Cache::forget('employees_data');
        
        // Get fresh data
        $lokasis = Lokasi::orderBy('nama_lokasi')->get();
        $kandangs = Kandang::with('lokasi')->orderBy('nama_kandang')->get();
        $pembibitans = Pembibitan::with(['lokasi', 'kandang'])->orderBy('judul')->get();
        $gudangs = Gudang::orderBy('nama')->get();
        $employees = Employee::where('jabatan', 'karyawan')->with(['lokasi', 'kandang'])->get();
        
        return response()->json([
            'success' => true,
            'data' => [
                'lokasis' => $lokasis,
                'kandangs' => $kandangs,
                'pembibitans' => $pembibitans,
                'gudangs' => $gudangs,
                'employees' => $employees
            ]
        ]);
    }

    /**
     * Update all existing absensi records with correct lokasi
     */
    public function updateAbsensiLokasi()
    {
        try {
            $absensis = Absensi::with('employee.lokasi')->get();
            $updatedCount = 0;
            
            foreach ($absensis as $absensi) {
                $newLokasi = 'Kantor Pusat'; // Default
                
                if ($absensi->employee_id) {
                    $employee = Employee::with('lokasi')->find($absensi->employee_id);
                    if ($employee && $employee->lokasi) {
                        $newLokasi = $employee->lokasi->nama_lokasi;
                    }
                }
                
                if ($absensi->lokasi_kerja !== $newLokasi) {
                    $absensi->update(['lokasi_kerja' => $newLokasi]);
                    $updatedCount++;
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => "Updated {$updatedCount} absensi records with correct lokasi",
                'updated_count' => $updatedCount
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating absensi lokasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function create()
    {
        // Clear ALL caches untuk memastikan data fresh
        \Cache::flush();
        
        // Get employees from employees table with fresh query - NO CACHE
        $query = Employee::orderBy('nama');
        
        // Admin can only see karyawan (not mandor)
        if (auth()->user()->isAdmin()) {
            $query->where('jabatan', 'karyawan');
        }
        
        $employees = $query->get();
        
        // Get gudang employees (karyawan gudang) - ALWAYS FRESH DATA
        $gudangEmployees = collect();
        if (auth()->user()->isManager()) {
            // Force fresh query without cache - DEBUG MODE
            \DB::enableQueryLog();
            $gudangs = \App\Models\Gudang::orderBy('nama')->get();
            $queries = \DB::getQueryLog();
            
            // Log the query for debugging
            \Log::info('Gudang query executed', [
                'query' => $queries[0]['query'] ?? 'No query',
                'bindings' => $queries[0]['bindings'] ?? [],
                'result_count' => $gudangs->count()
            ]);
            
            $gudangEmployees = $gudangs->map(function($gudang) {
                return (object) [
                'id' => 'gudang_' . $gudang->id,
                'nama' => $gudang->nama,
                    'jabatan' => 'karyawan_gudang',
                    'gaji_pokok' => $gudang->gaji,
                'source' => 'gudang'
                ];
            });
            
            // Log the result
            \Log::info('Gudang employees created', [
                'count' => $gudangEmployees->count(),
                'employees' => $gudangEmployees->pluck('nama')->toArray()
            ]);
        }
        
        // Get mandor employees
        $mandorEmployees = collect();
        if (auth()->user()->isManager()) {
            $mandorEmployees = \App\Models\Mandor::orderBy('nama')->get()->map(function($mandor) {
                return (object) [
                    'id' => 'mandor_' . $mandor->id,
                    'nama' => $mandor->nama,
                    'jabatan' => 'mandor',
                    'gaji_pokok' => $mandor->gaji,
                    'source' => 'mandor'
                ];
            });
        }
        
        // Combine all employees and remove duplicates by name
        // Prioritize gudang employees over regular employees for same name
        $allEmployees = $gudangEmployees->concat($employees->map(function($employee) {
            return (object) [
                'id' => 'employee_' . $employee->id,
                'nama' => $employee->nama,
                'jabatan' => $employee->jabatan,
                'gaji_pokok' => $employee->gaji_pokok,
                'source' => 'employee'
            ];
        }))->concat($mandorEmployees)
        ->unique('nama') // Remove duplicates by name (gudang employees will be kept first)
        ->sortBy('nama');
        
        $pembibitans = Pembibitan::with(['kandang', 'lokasi'])->orderBy('judul')->get();
        
        return view('absensis.create', compact('allEmployees', 'pembibitans'));
    }

    public function getSalary($employeeId)
    {
        try {
            $gaji = $this->absensiService->getLatestSalary($employeeId);
            
            Log::info('Salary requested', [
                'employee_id' => $employeeId,
                'salary' => $gaji
            ]);
            
            return response()->json(['gaji' => $gaji]);
        } catch (\Exception $e) {
            Log::error('Error getting salary: ' . $e->getMessage());
            return response()->json(['gaji' => 0], 500);
        }
    }

    public function store(StoreAbsensiRequest $request)
    {
        try {
            $validated = $request->validated();

        // Parse employee_id to get actual ID and source
        $employeeId = $validated['employee_id'];
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
        }

        // Get employee based on source
        $employee = null;
        if ($source === 'employee') {
            $employee = Employee::find($actualEmployeeId);
        } elseif ($source === 'gudang') {
            $gudang = \App\Models\Gudang::find($actualEmployeeId);
            if ($gudang) {
                $employee = (object) [
                    'id' => 'gudang_' . $gudang->id,
                    'nama' => $gudang->nama,
                    'jabatan' => 'karyawan_gudang',
                    'gaji_pokok' => $gudang->gaji,
                    'lokasi_kerja' => 'Kantor Pusat'
                ];
            }
        } elseif ($source === 'mandor') {
            $mandor = \App\Models\Mandor::find($actualEmployeeId);
            if ($mandor) {
                $employee = (object) [
                    'id' => 'mandor_' . $mandor->id,
                    'nama' => $mandor->nama,
                    'jabatan' => 'mandor',
                    'gaji_pokok' => $mandor->gaji,
                    'lokasi_kerja' => 'Kantor Pusat'
                ];
            }
        }
        
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan.'
            ], 400);
        }

        // Admin cannot create absensi for mandor employees
        if (auth()->user()->isAdmin() && $employee->jabatan === 'mandor') {
            return response()->json([
                'success' => false,
                'message' => 'Admin tidak dapat membuat absensi untuk karyawan mandor.'
            ], 403);
        }

        // Check for duplicate attendance on the same date
        $existingAbsensi = null;
        
        if ($source === 'employee') {
            $existingAbsensi = Absensi::where('employee_id', $actualEmployeeId)
                ->whereDate('tanggal', $validated['tanggal'])
                ->first();
        } else {
            // For gudang/mandor, check by name and date
            $existingAbsensi = Absensi::where('nama_karyawan', $employee->nama)
                ->whereDate('tanggal', $validated['tanggal'])
                ->first();
        }

        if ($existingAbsensi) {
            return response()->json([
                'success' => false,
                'message' => 'Data absensi untuk karyawan ini pada tanggal tersebut sudah ada.'
            ], 409);
        }

        // Get correct lokasi_kerja based on employee source - ALWAYS FRESH DATA
        $lokasiKerja = 'Kantor Pusat'; // Default
        
        if ($source === 'employee') {
            // ALWAYS get fresh data from database
            $employeeRecord = Employee::with('lokasi')->find($actualEmployeeId);
            if ($employeeRecord && $employeeRecord->lokasi) {
                $lokasiKerja = $employeeRecord->lokasi->nama_lokasi;
            } else {
                // If employee has no lokasi, assign first available lokasi
                $firstLokasi = \App\Models\Lokasi::orderBy('nama_lokasi')->first();
                if ($firstLokasi) {
                    $lokasiKerja = $firstLokasi->nama_lokasi;
                    // Update employee with this lokasi
                    $employeeRecord->update(['lokasi_id' => $firstLokasi->id]);
                }
            }
        } else {
            // For gudang/mandor, use their assigned location or default
            $lokasiKerja = $employee->lokasi_kerja ?? 'Kantor Pusat';
        }

        // Create absensi record (sesuai ERD)
        $data = [
            'employee_id' => $source === 'employee' ? $actualEmployeeId : null,
            'pembibitan_id' => $validated['pembibitan_id'],
            'nama_karyawan' => $employee->nama,
            'gaji_pokok_saat_itu' => $validated['gaji_pokok_saat_itu'],
            'tanggal' => $validated['tanggal'],
            'status' => $validated['status'],
            'gaji_hari_itu' => $validated['gaji_hari_itu'],
            'lokasi_kerja' => $lokasiKerja,
        ];

        $absensi = Absensi::create($data);

        // Clear caches to ensure real-time data
        // $this->dataSyncService->clearAbsensiCache();

        Log::info('Absensi created successfully', [
            'absensi_id' => $absensi->id,
            'employee_name' => $absensi->nama_karyawan,
            'date' => $absensi->tanggal
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data absensi berhasil ditambahkan.',
            'redirect' => route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index')
        ]);
        } catch (\Exception $e) {
            Log::error('Error storing absensi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data absensi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Absensi $absensi)
    {
        $absensi->load(['employee']);
        
        // Admin cannot view absensi for mandor employees
        if (auth()->user()->isAdmin() && $absensi->employee && $absensi->employee->jabatan === 'mandor') {
            abort(403, 'Admin tidak dapat melihat data absensi karyawan mandor.');
        }
        
        return view('absensis.show', compact('absensi'));
    }

    public function edit(Absensi $absensi)
    {
        // Admin cannot edit absensi for mandor employees
        if (auth()->user()->isAdmin() && $absensi->employee && $absensi->employee->jabatan === 'mandor') {
            abort(403, 'Admin tidak dapat mengedit data absensi karyawan mandor.');
        }
        
        // Get employees from employees table
        $query = Employee::orderBy('nama');
        
        // Admin can only see karyawan (not mandor)
        if (auth()->user()->isAdmin()) {
            $query->where('jabatan', 'karyawan');
        }
        
        $employees = $query->get();
        
        // Get gudang employees (karyawan gudang)
        $gudangEmployees = collect();
        if (auth()->user()->isManager()) {
            $gudangEmployees = \App\Models\Gudang::orderBy('nama')->get()->map(function($gudang) {
                return (object) [
                    'id' => 'gudang_' . $gudang->id,
                    'nama' => $gudang->nama,
                    'jabatan' => 'karyawan_gudang',
                    'gaji_pokok' => $gudang->gaji,
                    'source' => 'gudang'
                ];
            });
        }
        
        // Get mandor employees
        $mandorEmployees = collect();
        if (auth()->user()->isManager()) {
            $mandorEmployees = \App\Models\Mandor::orderBy('nama')->get()->map(function($mandor) {
                return (object) [
                    'id' => 'mandor_' . $mandor->id,
                    'nama' => $mandor->nama,
                    'jabatan' => 'mandor',
                    'gaji_pokok' => $mandor->gaji,
                    'source' => 'mandor'
                ];
            });
        }
        
        // Combine all employees
        $allEmployees = $employees->map(function($employee) {
            return (object) [
                'id' => 'employee_' . $employee->id,
                'nama' => $employee->nama,
                'jabatan' => $employee->jabatan,
                'gaji_pokok' => $employee->gaji_pokok,
                'source' => 'employee'
            ];
        })->concat($gudangEmployees)->concat($mandorEmployees)->sortBy('nama');
        
        return view('absensis.edit', compact('absensi', 'allEmployees'));
    }

    public function update(Request $request, Absensi $absensi)
    {
        $validated = $request->validate([
            'employee_id' => 'required|string',
            'tanggal' => 'required|date',
            'status' => 'required|in:full,setengah_hari',
        ]);

        // Parse employee_id to determine source and actual ID
        $employeeId = $validated['employee_id'];
        $actualEmployeeId = null;
        $employeeRole = null;
        $employeeName = null;

        if (str_starts_with($employeeId, 'employee_')) {
            // From employees table
            $actualEmployeeId = str_replace('employee_', '', $employeeId);
            $employee = Employee::find($actualEmployeeId);
            if ($employee) {
                $employeeRole = $employee->jabatan;
                $employeeName = $employee->nama;
            }
        } elseif (str_starts_with($employeeId, 'gudang_')) {
            // From gudangs table - create temporary employee record
            $gudangId = str_replace('gudang_', '', $employeeId);
            $gudang = \App\Models\Gudang::find($gudangId);
            if ($gudang) {
                // Create or find employee record for gudang
                $employee = Employee::firstOrCreate(
                    ['nama' => $gudang->nama, 'jabatan' => 'karyawan_gudang'],
                    ['gaji_pokok' => $gudang->gaji]
                );
                $actualEmployeeId = $employee->id;
                $employeeRole = 'karyawan_gudang';
                $employeeName = $gudang->nama;
            }
        } elseif (str_starts_with($employeeId, 'mandor_')) {
            // From mandors table - create temporary employee record
            $mandorId = str_replace('mandor_', '', $employeeId);
            $mandor = \App\Models\Mandor::find($mandorId);
            if ($mandor) {
                // Create or find employee record for mandor
                $employee = Employee::firstOrCreate(
                    ['nama' => $mandor->nama, 'jabatan' => 'mandor'],
                    ['gaji_pokok' => $mandor->gaji]
                );
                $actualEmployeeId = $employee->id;
                $employeeRole = 'mandor';
                $employeeName = $mandor->nama;
            }
        }

        if (!$actualEmployeeId) {
            return redirect()->back()
                ->with('error', 'Karyawan tidak ditemukan.')
                ->withInput();
        }

        // Admin cannot update absensi for mandor employees
        if (auth()->user()->isAdmin() && $employeeRole === 'mandor') {
            return redirect()->back()
                ->with('error', 'Admin tidak dapat mengupdate absensi untuk karyawan mandor.')
                ->withInput();
        }

        // Get correct lokasi_kerja for update
        $lokasiKerja = $absensi->lokasi_kerja; // Keep existing if no change needed
        
        if (str_starts_with($employeeId, 'employee_')) {
            // Get from employee's lokasi relationship
            $employeeRecord = Employee::with('lokasi')->find($actualEmployeeId);
            if ($employeeRecord && $employeeRecord->lokasi) {
                $lokasiKerja = $employeeRecord->lokasi->nama_lokasi;
            }
        }

        $data = [
            'employee_id' => $actualEmployeeId,
            'tanggal' => $validated['tanggal'],
            'status' => $validated['status'],
            'lokasi_kerja' => $lokasiKerja,
        ];

        $absensi->update($data);

        return redirect()->route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index')
                        ->with('success', 'Data absensi berhasil diperbarui.');
    }

    public function destroy(Absensi $absensi)
    {
        // Admin cannot delete absensi for mandor employees
        if (auth()->user()->isAdmin() && $absensi->employee && $absensi->employee->jabatan === 'mandor') {
            return redirect()->back()
                ->with('error', 'Admin tidak dapat menghapus data absensi karyawan mandor.');
        }

        $absensi->delete();

        return redirect()->route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index')
                        ->with('success', 'Data absensi berhasil dihapus.');
    }


    /**
     * Update existing attendance record
     */
    public function updateExisting(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:full,setengah_hari,absen',
        ]);

        // Find existing record
        $absensi = Absensi::where('employee_id', $validated['employee_id'])
            ->where('tanggal', $validated['tanggal'])
            ->first();

        if ($absensi) {
            // Update existing record
            $absensi->update(['status' => $validated['status']]);
            
            
            return response()->json(['success' => true, 'message' => 'Data absensi berhasil diperbarui']);
        } else {
            // Create new record if not exists
            $data = [
                'employee_id' => $validated['employee_id'],
                'tanggal' => $validated['tanggal'],
                'status' => $validated['status'],
            ];

            $absensi = Absensi::create($data);
            
            return response()->json(['success' => true, 'message' => 'Data absensi berhasil ditambahkan']);
        }
    }

    /**
     * Get employees for AJAX dropdown
     */
    public function getEmployees(Request $request)
    {
        // Clear cache untuk memastikan data fresh
        \Cache::forget('employees_data');
        \Cache::forget('gudangs_data');
        
        $query = Employee::select('id', 'nama', 'jabatan');
        
        // Admin can only see karyawan (not mandor)
        if (auth()->user()->isAdmin()) {
            $query->where('jabatan', 'karyawan');
        }
        
        $employees = $query->get();
        
        // Get fresh gudang data for manager
        $gudangEmployees = collect();
        if (auth()->user()->isManager()) {
            $gudangs = \App\Models\Gudang::select('id', 'nama', 'gaji')->orderBy('nama')->get();
            $gudangEmployees = $gudangs->map(function($gudang) {
                return (object) [
                    'id' => 'gudang_' . $gudang->id,
                    'nama' => $gudang->nama,
                    'jabatan' => 'karyawan_gudang',
                    'gaji_pokok' => $gudang->gaji,
                    'source' => 'gudang'
                ];
            });
        }
        
        // Combine employees and gudang employees
        $allEmployees = $employees->concat($gudangEmployees);
        
        return response()->json($allEmployees);
    }
}