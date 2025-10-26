@extends('layouts.app')

@section('title', 'Master Absensi & Kalender')

@push('styles')
<style>
/* Excel-like DataTables Styling */
.dataTables_wrapper {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
    color: #333;
    font-size: 14px;
}

.dataTables_wrapper .dataTables_length select {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 4px 8px;
    background: white;
}

.dataTables_wrapper .dataTables_filter input {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 6px 12px;
    margin-left: 8px;
    background: white;
}

.dataTables_wrapper .dataTables_filter input:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

/* Excel-like table styling */
#absensiTable {
    border-collapse: collapse;
    width: 100%;
    background: white;
    border: 1px solid #ddd;
}

#absensiTable thead th {
    background: #2c3e50 !important;
    border: 1px solid #ddd;
    padding: 12px 8px;
    font-weight: 600;
    color: #ffffff !important;
    text-align: center;
    position: sticky;
    top: 0;
    z-index: 10;
}

#absensiTable tbody td {
    border: 1px solid #ddd;
    padding: 8px;
    vertical-align: middle;
    background: white;
}

#absensiTable tbody tr:hover {
    background-color: #f5f5f5;
}

#absensiTable tbody tr:nth-child(even) {
    background-color: #fafafa;
}

#absensiTable tbody tr:nth-child(even):hover {
    background-color: #f0f0f0;
}

/* Excel-like buttons */
.dt-buttons {
    margin-bottom: 20px;
}

.dt-buttons .btn {
    background: #007bff;
    border: 1px solid #007bff;
    color: white;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 13px;
    margin-right: 5px;
    transition: all 0.2s;
}

.dt-buttons .btn:hover {
    background: #0056b3;
    border-color: #0056b3;
    color: white;
}

.dt-buttons .btn:active {
    background: #004085;
    border-color: #004085;
}

/* Status badges */
.badge {
    font-size: 11px;
    padding: 4px 8px;
    border-radius: 12px;
    font-weight: 500;
}

.badge.bg-success {
    background-color: #28a745 !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #ffffff !important;
}

.badge.bg-secondary {
    background-color: #6c757d !important;
}

/* Action buttons */
.btn-group .btn {
    padding: 4px 8px;
    font-size: 12px;
    border-radius: 4px;
    margin: 0 1px;
    color: #ffffff !important;
    font-weight: 500;
}

.btn-warning {
    background-color: #ffc107 !important;
    border-color: #ffc107 !important;
    color: #212529 !important;
}

.btn-danger {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
    color: #ffffff !important;
}

/* Ensure action buttons are visible */
.btn-group .btn:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

/* Fix any text visibility issues in action column */
#absensiTable tbody td:last-child {
    background-color: #ffffff !important;
    color: #333 !important;
}

/* Responsive design */
@media (max-width: 768px) {
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 10px;
    }
    
    .dt-buttons {
        margin-bottom: 15px;
    }
    
    .dt-buttons .btn {
        font-size: 11px;
        padding: 4px 8px;
        margin-right: 3px;
    }
}

/* Excel-like pagination */
.dataTables_wrapper .dataTables_paginate .paginate_button {
    border: 1px solid #ddd;
    background: white;
    color: #333;
    padding: 6px 12px;
    margin: 0 2px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #f8f9fa;
    border-color: #007bff;
    color: #007bff;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #007bff;
    border-color: #007bff;
    color: white;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    background: #f8f9fa;
    border-color: #ddd;
    color: #6c757d;
    cursor: not-allowed;
}

/* Loading spinner */
.dataTables_processing {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid #ddd;
    border-radius: 4px;
    color: #333;
    font-weight: 500;
}

/* Info display */
.dataTables_info {
    color: #6c757d;
    font-size: 13px;
    margin-top: 10px;
}

/* Override DataTables Bootstrap theme for better header visibility */
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
    color: #333;
    font-size: 14px;
}

/* Force header text to be white and visible */
#absensiTable thead th,
#absensiTable thead th * {
    color: #ffffff !important;
    background-color: #2c3e50 !important;
}

/* Override any Bootstrap DataTables theme */
table.dataTable thead th {
    background-color: #2c3e50 !important;
    color: #ffffff !important;
    border-color: #ddd !important;
}

table.dataTable thead th.sorting,
table.dataTable thead th.sorting_asc,
table.dataTable thead th.sorting_desc {
    background-color: #2c3e50 !important;
    color: #ffffff !important;
}

/* Ensure all header text is visible */
#absensiTable thead th {
    text-shadow: none !important;
    font-weight: 600 !important;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-table me-2"></i>
                        Transaksi Absensi
                    </h4>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success" onclick="showBulkAttendance()">
                            <i class="bi bi-people-fill me-1"></i>
                            Tambah Cepat Absensi
                        </button>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.absensis.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>
                                Tambah Absensi
                            </a>
                        @else
                            <a href="{{ route('manager.absensis.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>
                                Tambah Absensi
                            </a>
                        @endif
                        <button type="button" class="btn btn-danger" id="deleteSelectedBtn" onclick="deleteSelectedAbsensi()" disabled>
                            <i class="bi bi-trash me-1"></i>
                            Hapus
                        </button>
                    </div>
    </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="tanggal_filter" class="form-label">Filter Tanggal:</label>
                            <input type="date" id="tanggal_filter" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="bibit_filter" class="form-label">Cari Bibit/Pembibitan:</label>
                            <input type="text" id="bibit_filter" class="form-control" placeholder="Masukkan nama pembibitan...">
</div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button id="filterBtn" class="btn btn-primary me-2">
                                    <i class="bi bi-search"></i> Filter
                                </button>
                                <button id="resetBtn" class="btn btn-secondary">
                                    <i class="bi bi-arrow-clockwise"></i> Reset
                    </button>
                </div>
            </div>
</div>

                    <div class="table-responsive">
                        <table id="absensiTable" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="3%">
                                        <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()">
                                    </th>
                                    <th width="5%">No</th>
                                    <th width="10%">Tanggal</th>
                                    <th width="18%">Nama Karyawan</th>
                                    <th width="10%">Role</th>
                                    <th width="10%">Status</th>
                                    <th width="12%">Lokasi</th>
                                    <th width="12%">Pembibitan</th>
                                    <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                                <!-- Data akan dimuat via DataTables AJAX -->
                    </tbody>
                </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Attendance Modal -->
<div class="modal fade" id="bulkAttendanceModal" tabindex="-1" aria-labelledby="bulkAttendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="bulkAttendanceModalLabel">
                    <i class="bi bi-people-fill me-2"></i>Tambah Cepat Absensi - Absensi Massal
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Filter Section -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Filter Pembibitan</label>
                        <select class="form-select" id="filterPembibitanBulk" onchange="filterEmployeesBulk()">
                            <option value="">Semua Pembibitan</option>
                            @foreach($pembibitans as $pembibitan)
                                <option value="{{ $pembibitan->id }}">{{ $pembibitan->judul }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tanggal Absensi</label>
                        <input type="date" class="form-control" id="tanggalBulk" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <!-- Bulk Attendance Form -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAllBulk" onchange="toggleSelectAll()">
                            <label class="form-check-label fw-bold" for="selectAllBulk">
                                Pilih Semua Karyawan
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <button type="button" class="btn btn-success" onclick="submitBulkAttendance()">
                            <i class="bi bi-save me-1"></i>Simpan Tambah Cepat Absensi
                        </button>
                    </div>
                </div>

                <!-- Employees List -->
                <div class="table-responsive">
                    <table class="table table-hover" id="employeesTableBulk">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">Pilih</th>
                                <th width="20%">Nama Karyawan</th>
                                <th width="15%">Lokasi</th>
                                <th width="15%">Kandang</th>
                                <th width="10%">Gaji Pokok</th>
                                <th width="15%">Status</th>
                                <th width="15%">Pembibitan</th>
                                <th width="5%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="employeesTableBodyBulk">
                            @foreach($employees as $employee)
                            <tr data-employee-id="{{ $employee->id }}">
                                <td>
                                    <input type="checkbox" class="form-check-input employee-checkbox-bulk" 
                                           value="{{ $employee->id }}" 
                                           data-employee='{{ json_encode($employee) }}'>
                                </td>
                                <td>
                                    <strong>{{ $employee->nama }}</strong>
                                    <br><small class="text-muted">{{ $employee->jabatan === 'karyawan' ? 'karyawan kandang' : $employee->jabatan }}</small>
                                </td>
                                <td>{{ $employee->lokasi->nama_lokasi ?? 'N/A' }}</td>
                                <td>{{ $employee->kandang->nama_kandang ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-info">Rp {{ number_format($employee->gaji_pokok, 0, ',', '.') }}</span>
                                </td>
                                <td>
                                    <select class="form-select form-select-sm status-select-bulk" data-employee-id="{{ $employee->id }}">
                                        <option value="full">Full Day</option>
                                        <option value="setengah_hari">½ Hari</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-select form-select-sm pembibitan-select-bulk" data-employee-id="{{ $employee->id }}">
                                        <option value="">Pilih Pembibitan</option>
                                        @foreach($pembibitans as $pembibitan)
                                            <option value="{{ $pembibitan->id }}">
                                                {{ $pembibitan->judul }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm" 
                                            onclick="quickAbsenBulk('{{ $employee->id }}')">
                                        <i class="bi bi-lightning"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Attendance Modal -->
<div class="modal fade" id="quickAttendanceModalBulk" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-lightning me-2"></i>Quick Absensi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="quickAttendanceFormBulk">
                    @csrf
                    <input type="hidden" id="quickEmployeeIdBulk">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Karyawan</label>
                        <input type="text" class="form-control" id="quickEmployeeNameBulk" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal</label>
                        <input type="date" class="form-control" id="quickTanggalBulk" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="quickStatusBulk" id="quickFullBulk" value="full" checked>
                                    <label class="form-check-label" for="quickFullBulk">
                                        Full Day
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="quickStatusBulk" id="quickHalfBulk" value="setengah_hari">
                                    <label class="form-check-label" for="quickHalfBulk">
                                        ½ Hari
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pembibitan (Opsional)</label>
                        <select class="form-select" id="quickPembibitanBulk">
                            <option value="">Pilih Pembibitan</option>
                            @foreach($pembibitans as $pembibitan)
                                <option value="{{ $pembibitan->id }}">
                                    {{ $pembibitan->judul }} - {{ $pembibitan->kandang->nama_kandang ?? 'N/A' }} ({{ $pembibitan->lokasi->nama_lokasi ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" onclick="submitQuickAttendanceBulk()">
                    <i class="bi bi-save me-1"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#absensiTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ auth()->user()->isAdmin() ? route('admin.absensis.index') : route('manager.absensis.index') }}",
            type: 'GET',
            cache: false, // Disable caching for real-time updates
            data: function(d) {
                // Force fresh data with timestamp
                d._t = new Date().getTime();
                d._token = $('meta[name="csrf-token"]').attr('content');
                d.tanggal_filter = $('#tanggal_filter').val();
                d.bibit_filter = $('#bibit_filter').val();
                d._t = new Date().getTime(); // Cache busting for real-time updates
            }
        },
        columns: [
            { 
                data: null,
                orderable: false, 
                searchable: false,
                width: '3%',
                title: '<input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()">',
                render: function(data, type, row) {
                    return '<input type="checkbox" class="absensi-checkbox" value="' + row.id + '" onchange="updateDeleteButton()">';
                }
            },
            { 
                data: 'DT_RowIndex', 
                name: 'DT_RowIndex', 
                orderable: false, 
                searchable: false,
                width: '5%',
                title: 'No'
            },
            { 
                data: 'nama_karyawan', 
                name: 'nama_karyawan',
                width: '18%',
                title: 'Nama Karyawan'
            },
            { 
                data: 'role_karyawan', 
                name: 'role_karyawan',
                width: '12%',
                title: 'Role',
                render: function(data, type, row) {
                    if (data === 'karyawan') {
                        return 'karyawan kandang';
                    } else if (data === 'karyawan_gudang') {
                        return 'karyawan gudang';
                    }
                    return data;
                }
            },
            { 
                data: 'tanggal_formatted', 
                name: 'tanggal',
                width: '10%',
                title: 'Tanggal'
            },
            { 
                data: 'status_badge', 
                name: 'status',
                orderable: false,
                searchable: false,
                width: '10%',
                title: 'Status'
            },
            { 
                data: 'lokasi_kerja', 
                name: 'lokasi_kerja',
                width: '10%',
                title: 'Lokasi'
            },
            { 
                data: 'pembibitan_info', 
                name: 'pembibitan_info',
                orderable: false,
                searchable: false,
                width: '10%',
                title: 'Pembibitan'
            },
            { 
                data: 'action', 
                name: 'action',
                orderable: false,
                searchable: false,
                width: '13%',
                title: 'Aksi'
            }
        ],
        order: [[4, 'desc']], // Sort by tanggal descending
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        language: {
            processing: "Memproses data...",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            zeroRecords: "Tidak ada data yang ditemukan",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            search: "Cari:",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="bi bi-file-excel"></i> Excel',
                className: 'btn btn-success',
                title: 'Master Absensi & Kalender',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6],
                    format: {
                        body: function (data, row, column, node) {
                            return data.replace(/<[^>]*>/g, '');
                        }
                    }
                }
            },
            {
                extend: 'pdf',
                text: '<i class="bi bi-file-pdf"></i> PDF',
                className: 'btn btn-danger',
                title: 'Master Absensi & Kalender',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6],
                    format: {
                        body: function (data, row, column, node) {
                            return data.replace(/<[^>]*>/g, '');
                        }
                    }
                }
            },
            {
                extend: 'print',
                text: '<i class="bi bi-printer"></i> Print',
                className: 'btn btn-info',
                title: 'Master Absensi & Kalender',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6],
                    format: {
                        body: function (data, row, column, node) {
                            return data.replace(/<[^>]*>/g, '');
                        }
                    }
                }
            },
            {
                extend: 'copy',
                text: '<i class="bi bi-clipboard"></i> Copy',
                className: 'btn btn-secondary',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6],
                    format: {
                        body: function (data, row, column, node) {
                            return data.replace(/<[^>]*>/g, '');
                        }
                    }
                }
            }
        ],
        responsive: true,
        scrollX: true,
        autoWidth: false,
        columnDefs: [
            {
                targets: [0, 4, 5, 7], // No, Tanggal, Status, Aksi
                className: 'text-center'
            },
            {
                targets: [3], // Gaji
                className: 'text-end'
            }
        ]
    });
    
    // Filter functionality
    $('#filterBtn').on('click', function() {
        $('#absensiTable').DataTable().ajax.reload();
    });
    
    // Reset functionality
    $('#resetBtn').on('click', function() {
        $('#tanggal_filter').val('');
        $('#bibit_filter').val('');
        $('#absensiTable').DataTable().ajax.reload();
    });
    
    // Real-time refresh master data
    function refreshMasterData() {
        fetch('{{ route(auth()->user()->isManager() ? "manager.absensis.refresh-master-data" : "admin.absensis.refresh-master-data") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update filter dropdowns
                    updateFilterDropdowns(data.data);
                    console.log('✅ Master data refreshed');
                }
            })
            .catch(error => {
                console.error('Error refreshing master data:', error);
            });
    }
    
    // Update filter dropdowns with fresh data
    function updateFilterDropdowns(data) {
    }
    
    // Auto-refresh master data every 5 minutes (reduced frequency)
    setInterval(refreshMasterData, 300000);
    
    // Update absensi lokasi every 2 minutes (reduced frequency)
    setInterval(updateAbsensiLokasi, 120000);
    
    // Refresh bulk attendance data every 3 minutes (reduced frequency)
    setInterval(refreshBulkAttendanceData, 180000);
    
    // Function to update absensi lokasi
    function updateAbsensiLokasi() {
        fetch('{{ route(auth()->user()->isManager() ? "manager.absensis.update-lokasi" : "admin.absensis.update-lokasi") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.updated_count > 0) {
                console.log('✅ Updated ' + data.updated_count + ' absensi records with correct lokasi');
                // Refresh table to show updated data
                $('#absensiTable').DataTable().ajax.reload(null, false);
            }
        })
        .catch(error => {
            console.error('Error updating absensi lokasi:', error);
        });
    }
    
    // Function to refresh bulk attendance data
    function refreshBulkAttendanceData() {
        // Only refresh if bulk modal is open
        const bulkModal = document.getElementById('bulkAttendanceModal');
        if (bulkModal && bulkModal.classList.contains('show')) {
            fetch('{{ route(auth()->user()->isManager() ? "manager.absensis.refresh-master-data" : "admin.absensis.refresh-master-data") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update employee list in bulk modal
                        updateBulkEmployeeList(data.data.employees);
                        console.log('✅ Bulk attendance data refreshed');
                    }
                })
                .catch(error => {
                    console.error('Error refreshing bulk attendance data:', error);
                });
        }
    }
    
    // Function to update bulk employee list
    function updateBulkEmployeeList(employees) {
        const tbody = document.getElementById('employeesTableBodyBulk');
        if (!tbody) return;
        
        // Clear existing rows
        tbody.innerHTML = '';
        
        // Add new rows
        employees.forEach(employee => {
            const row = document.createElement('tr');
            row.setAttribute('data-employee-id', employee.id);
            row.innerHTML = `
                <td>
                    <input type="checkbox" class="form-check-input employee-checkbox-bulk" 
                           value="${employee.id}" 
                           data-employee='${JSON.stringify(employee)}'>
                </td>
                <td>
                    <strong>${employee.nama}</strong>
                    <br><small class="text-muted">${employee.jabatan === 'karyawan' ? 'karyawan kandang' : (employee.jabatan === 'karyawan_gudang' ? 'karyawan gudang' : employee.jabatan)}</small>
                </td>
                <td>${employee.lokasi ? employee.lokasi.nama_lokasi : 'N/A'}</td>
                <td>${employee.kandang ? employee.kandang.nama_kandang : 'N/A'}</td>
                <td>
                    <span class="badge bg-info">Rp ${new Intl.NumberFormat('id-ID').format(employee.gaji_pokok)}</span>
                </td>
                <td>
                    <select class="form-select form-select-sm status-select-bulk" data-employee-id="${employee.id}">
                        <option value="full">Full Day</option>
                        <option value="setengah_hari">½ Hari</option>
                    </select>
                </td>
                <td>
                    <select class="form-select form-select-sm pembibitan-select-bulk" data-employee-id="${employee.id}">
                        <option value="">Pilih Pembibitan</option>
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-warning btn-sm" onclick="quickAbsenBulk('${employee.id}')">
                        <i class="bi bi-lightning-fill"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }
    
        // Force refresh on page load
        setTimeout(function() {
            $('#absensiTable').DataTable().ajax.reload(null, false);
        }, 1000);
        
        // Auto-refresh every 5 seconds for real-time updates
        setInterval(function() {
            $('#absensiTable').DataTable().ajax.reload(null, false); // false = keep current page
        }, 5000); // Refresh every 5 seconds for real-time updates
    
    // Real-time search on bibit filter
    $('#bibit_filter').on('keyup', function() {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(function() {
            $('#absensiTable').DataTable().ajax.reload();
        }, 500);
    });
});

// Tambah Cepat Absensi Functions
function showBulkAttendance() {
    const modal = new bootstrap.Modal(document.getElementById('bulkAttendanceModal'));
    modal.show();
    
    // Load employees when modal opens
    loadBulkEmployees();
}

// Load employees for bulk attendance
function loadBulkEmployees() {
    fetch('{{ route(auth()->user()->isManager() ? "manager.absensis.refresh-master-data" : "admin.absensis.refresh-master-data") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateBulkEmployeeList(data.data.employees);
                console.log('✅ Bulk employees loaded:', data.data.employees.length);
            }
        })
        .catch(error => {
            console.error('Error loading bulk employees:', error);
        });
}

// Filter employees by pembibitan
function filterEmployeesBulk() {
    const pembibitanId = document.getElementById('filterPembibitanBulk').value;
    
    fetch('{{ route(auth()->user()->isManager() ? "manager.absensis.refresh-master-data" : "admin.absensis.refresh-master-data") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let filteredEmployees = data.data.employees;
                
                // Filter by pembibitan if selected
                if (pembibitanId) {
                    filteredEmployees = filteredEmployees.filter(employee => {
                        // Check if employee has attendance records in the selected pembibitan
                        return employee.absensis && employee.absensis.some(absensi => 
                            absensi.pembibitan_id == pembibitanId
                        );
                    });
                }
                
                updateBulkEmployeeList(filteredEmployees);
                console.log('✅ Filtered employees by pembibitan:', filteredEmployees.length);
            }
        })
        .catch(error => {
            console.error('Error filtering bulk employees:', error);
        });
}

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAllBulk');
    const checkboxes = document.querySelectorAll('.employee-checkbox-bulk');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function submitBulkAttendance() {
    const selectedEmployees = [];
    const tanggal = document.getElementById('tanggalBulk').value;
    
    if (!tanggal) {
        alert('Mohon pilih tanggal absensi');
        return;
    }

    document.querySelectorAll('.employee-checkbox-bulk:checked').forEach(checkbox => {
        const employeeId = checkbox.value;
        const statusSelect = document.querySelector(`.status-select-bulk[data-employee-id="${employeeId}"]`);
        const pembibitanSelect = document.querySelector(`.pembibitan-select-bulk[data-employee-id="${employeeId}"]`);
        
        selectedEmployees.push({
            id: employeeId,
            status: statusSelect.value,
            pembibitan_id: pembibitanSelect.value || null
        });
    });

    if (selectedEmployees.length === 0) {
        alert('Mohon pilih minimal satu karyawan');
        return;
    }

    // Show loading
    const submitBtn = event.target;
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Menyimpan...';

    fetch('{{ route("manager.absensis.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            tanggal: tanggal,
            employees: selectedEmployees
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Tambah cepat absensi berhasil! ${data.success_count} berhasil, ${data.error_count} gagal`);
            if (data.errors.length > 0) {
                console.log('Errors:', data.errors);
            }
            // Refresh table
            $('#absensiTable').DataTable().ajax.reload();
            bootstrap.Modal.getInstance(document.getElementById('bulkAttendanceModal')).hide();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan: ' + error.message);
    })
    .finally(() => {
        // Restore button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

function quickAbsenBulk(employeeId) {
    const employee = document.querySelector(`[data-employee-id="${employeeId}"]`);
    const employeeData = JSON.parse(employee.querySelector('.employee-checkbox-bulk').getAttribute('data-employee'));
    
    document.getElementById('quickEmployeeIdBulk').value = employeeId;
    document.getElementById('quickEmployeeNameBulk').value = employeeData.nama;
    
    const modal = new bootstrap.Modal(document.getElementById('quickAttendanceModalBulk'));
        modal.show();
    }

function submitQuickAttendanceBulk() {
    const employeeId = document.getElementById('quickEmployeeIdBulk').value;
    const tanggal = document.getElementById('quickTanggalBulk').value;
    const status = document.querySelector('input[name="quickStatusBulk"]:checked').value;
    const pembibitanId = document.getElementById('quickPembibitanBulk').value;

    if (!tanggal) {
        alert('Mohon pilih tanggal');
        return;
    }

    fetch('{{ route("manager.absensis.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            employee_id: employeeId,
            tanggal: tanggal,
            status: status,
            pembibitan_id: pembibitanId || null
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Absensi berhasil disimpan!');
            bootstrap.Modal.getInstance(document.getElementById('quickAttendanceModalBulk')).hide();
            $('#absensiTable').DataTable().ajax.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan: ' + error.message);
    });
    }

// Bulk Delete Functions
function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.absensi-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateDeleteButton();
}

function updateDeleteButton() {
    const checkboxes = document.querySelectorAll('.absensi-checkbox:checked');
    const deleteBtn = document.getElementById('deleteSelectedBtn');
    
    if (checkboxes.length > 0) {
        deleteBtn.disabled = false;
        deleteBtn.textContent = `Hapus (${checkboxes.length})`;
    } else {
        deleteBtn.disabled = true;
        deleteBtn.textContent = 'Hapus';
    }
}

function deleteSelectedAbsensi() {
    const checkboxes = document.querySelectorAll('.absensi-checkbox:checked');
    
    if (checkboxes.length === 0) {
        alert('Pilih absensi yang akan dihapus terlebih dahulu!');
        return;
    }
    
    const selectedIds = Array.from(checkboxes).map(checkbox => checkbox.value);
    
    if (confirm(`Apakah Anda yakin ingin menghapus ${selectedIds.length} absensi yang dipilih?`)) {
        // Show loading
        const deleteBtn = document.getElementById('deleteSelectedBtn');
        const originalText = deleteBtn.innerHTML;
        deleteBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Menghapus...';
        deleteBtn.disabled = true;
        
        // Send delete request
        fetch('{{ route(auth()->user()->isAdmin() ? "admin.absensis.bulk-delete" : "manager.absensis.bulk-delete") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                ids: selectedIds
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Berhasil menghapus ${data.deleted_count} absensi!`);
                // Reload DataTables
                $('#absensiTable').DataTable().ajax.reload();
                // Reset select all checkbox
                document.getElementById('selectAllCheckbox').checked = false;
                updateDeleteButton();
            } else {
                alert('Gagal menghapus absensi: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan: ' + error.message);
        })
        .finally(() => {
            // Restore button
            deleteBtn.innerHTML = originalText;
            deleteBtn.disabled = false;
        });
    }
}
</script>

<style>
/* Fix table header visibility */
#absensiTable thead th {
    background-color: #1e40af !important;
    color: white !important;
    font-weight: 600;
    text-align: center;
    vertical-align: middle;
    padding: 12px 8px;
    border: none;
    font-size: 0.875rem;
}

/* Fix DataTables controls visibility */
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
    color: #333 !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    color: #333 !important;
}
</style>
@endpush