@extends('layouts.app')

@section('title', 'Master Absensi')

@push('styles')
<style>
/* Modern Design System */
:root {
    --primary-color: #2563eb;
    --secondary-color: #64748b;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --light-bg: #ffffff;
    --border-color: #d1d5db;
    --text-primary: #111827;
    --text-secondary: #374151;
    --text-muted: #6b7280;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
    --radius-sm: 0.375rem;
    --radius-md: 0.5rem;
    --radius-lg: 0.75rem;
}

/* Enhanced Avatar */
.avatar-sm {
    width: 48px;
    height: 48px;
    font-size: 18px;
    background: linear-gradient(135deg, var(--primary-color), #3b82f6);
    border: 2px solid #ffffff;
    box-shadow: var(--shadow-sm);
    color: white !important;
}

/* Modern Table Design */
.table {
    border-collapse: separate;
    border-spacing: 0;
    background: white;
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-md);
}

.table th {
    border: none;
    font-weight: 700;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    background: linear-gradient(135deg, #1e293b, #334155);
    color: #ffffff !important;
    padding: 1rem 0.75rem;
    position: relative;
    text-align: center;
}

.table th:first-child {
    border-top-left-radius: var(--radius-lg);
}

.table th:last-child {
    border-top-right-radius: var(--radius-lg);
}

.table td {
    vertical-align: middle;
    padding: 1rem 0.75rem;
    border: none;
    border-bottom: 1px solid var(--border-color);
    background: #ffffff;
    transition: all 0.2s ease;
    color: var(--text-primary) !important;
}

.table tbody tr {
    transition: all 0.2s ease;
}

.table tbody tr:hover {
    background: #f8fafc !important;
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.table tbody tr:hover td {
    color: var(--text-primary) !important;
}

.table tbody tr:last-child td:first-child {
    border-bottom-left-radius: var(--radius-lg);
}

.table tbody tr:last-child td:last-child {
    border-bottom-right-radius: var(--radius-lg);
}

/* Enhanced Badges */
.badge {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: var(--radius-md);
    text-transform: uppercase;
    letter-spacing: 0.025em;
    box-shadow: var(--shadow-sm);
    color: #ffffff !important;
}

.badge-gudang {
    background: linear-gradient(135deg, var(--primary-color), #3b82f6);
    color: #ffffff !important;
}

.badge-mandor {
    background: linear-gradient(135deg, var(--success-color), #34d399);
    color: #ffffff !important;
}

/* Status Badges */
.badge-success {
    background: linear-gradient(135deg, var(--success-color), #34d399);
    color: #ffffff !important;
}

.badge-warning {
    background: linear-gradient(135deg, var(--warning-color), #fbbf24);
    color: #ffffff !important;
}

/* Modern Buttons */
.btn-group-sm .btn {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    border-radius: var(--radius-sm);
    border: none;
    font-weight: 500;
    transition: all 0.2s ease;
    box-shadow: var(--shadow-sm);
}

.btn-outline-info {
    background: white;
    color: #0ea5e9;
    border: 2px solid #0ea5e9;
}

.btn-outline-info:hover {
    background: #0ea5e9;
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.btn-outline-warning {
    background: white;
    color: var(--warning-color);
    border: 2px solid var(--warning-color);
}

.btn-outline-warning:hover {
    background: var(--warning-color);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.btn-outline-danger {
    background: white;
    color: var(--danger-color);
    border: 2px solid var(--danger-color);
}

.btn-outline-danger:hover {
    background: var(--danger-color);
    color: white;
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

/* Enhanced Cards */
.card {
    border: none;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    background: white;
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border: none;
    font-weight: 700;
    padding: 1.5rem;
    color: var(--text-primary);
}

.card-body {
    padding: 2rem;
}

/* Statistics Cards Enhancement */
.stats-card {
    background: #ffffff;
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--border-color);
    transition: all 0.2s ease;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.stats-card h6 {
    color: var(--text-secondary) !important;
    font-weight: 600;
}

.stats-card h3 {
    color: var(--text-primary) !important;
    font-weight: 800;
}

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-bottom: 1rem;
}

.stats-icon.primary {
    background: linear-gradient(135deg, var(--primary-color), #3b82f6);
    color: #ffffff !important;
}

.stats-icon.success {
    background: linear-gradient(135deg, var(--success-color), #34d399);
    color: #ffffff !important;
}

.stats-icon.warning {
    background: linear-gradient(135deg, var(--warning-color), #fbbf24);
    color: #ffffff !important;
}

/* Search Form Enhancement */
.search-form {
    background: #ffffff;
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--border-color);
}

.search-form label {
    color: var(--text-primary) !important;
    font-weight: 600;
}

.form-control {
    border: 2px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    color: var(--text-primary) !important;
    background: #ffffff;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgb(37 99 235 / 0.1);
    color: var(--text-primary) !important;
}

.form-control::placeholder {
    color: var(--text-muted) !important;
}

/* Page Header */
.page-header {
    background: linear-gradient(135deg, #1e293b, #334155);
    color: #ffffff !important;
    padding: 2rem;
    border-radius: var(--radius-lg);
    margin-bottom: 2rem;
    box-shadow: var(--shadow-lg);
}

.page-title {
    font-size: 2rem;
    font-weight: 800;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 1rem;
    color: #ffffff !important;
}

.page-title i {
    font-size: 2.5rem;
    opacity: 0.9;
    color: #ffffff !important;
}

/* Responsive Design */
@media (max-width: 768px) {
    .table-responsive {
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .stats-card {
        margin-bottom: 1rem;
    }
}

/* Loading Animation */
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.loading {
    animation: pulse 2s infinite;
}

/* Custom Scrollbar */
.table-responsive::-webkit-scrollbar {
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: var(--radius-sm);
}

.table-responsive::-webkit-scrollbar-thumb {
    background: var(--primary-color);
    border-radius: var(--radius-sm);
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #1d4ed8;
}

/* Force Text Visibility */
body {
    color: var(--text-primary) !important;
}

.text-dark {
    color: var(--text-primary) !important;
}

.text-muted {
    color: var(--text-muted) !important;
}

.text-primary {
    color: var(--primary-color) !important;
}

.text-success {
    color: var(--success-color) !important;
}

.text-warning {
    color: var(--warning-color) !important;
}

/* Table Text Override */
.table td strong {
    color: var(--text-primary) !important;
}

.table td small {
    color: var(--text-muted) !important;
}

/* Alert Text */
.alert {
    color: var(--text-primary) !important;
}

.alert-light {
    background-color: #f8f9fa !important;
    border-color: var(--border-color) !important;
    color: var(--text-primary) !important;
}
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="bi bi-calendar-check"></i> Master Absensi
        </h1>
        <a href="{{ route(auth()->user()->isManager() ? 'manager.absensis.create' : 'admin.absensis.create') }}" 
           class="btn btn-light btn-lg px-4 py-2" style="border-radius: var(--radius-md); font-weight: 600;">
            <i class="bi bi-plus-circle me-2"></i> Tambah Absensi
        </a>
    </div>
</div>

<!-- Enhanced Search Form -->
<div class="search-form mb-4">
    <form method="GET" action="{{ route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index') }}">
        <div class="row align-items-end">
            <div class="col-md-8">
                <label for="search" class="form-label fw-semibold">
                    <i class="bi bi-search me-1"></i> Pencarian
                </label>
                <div class="input-group">
                    <input type="text" class="form-control" id="search" name="search"
                           value="{{ request('search') }}" 
                           placeholder="Cari berdasarkan status, nama karyawan, atau master data...">
                    <button class="btn btn-primary px-4" type="submit" style="border-radius: 0 var(--radius-md) var(--radius-md) 0;">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                </div>
            </div>
            <div class="col-md-4">
                <a href="{{ route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index') }}" 
                   class="btn btn-outline-secondary px-4" style="border-radius: var(--radius-md);">
                    <i class="bi bi-arrow-clockwise me-1"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Absensis Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Daftar Absensi</h5>
    </div>
    <div class="card-body">
        @if($absensis->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="25%">Nama Karyawan</th>
                            <th width="10%" class="text-center">Tipe</th>
                            <th width="15%" class="text-center">Tanggal</th>
                            <th width="15%" class="text-center">Status</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($absensis as $index => $absensi)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2">
                                            <i class="bi bi-person-fill text-primary"></i>
                                        </div>
                                        <div>
                                            <strong class="text-dark">{{ $absensi->employee->nama ?? 'Karyawan Tidak Ditemukan' }}</strong>
                                            <br>
                                            <small class="text-muted">ID: {{ $absensi->employee_id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $absensi->employee && $absensi->employee->role == 'mandor' ? 'success' : 'primary' }} px-3 py-2">
                                        <i class="bi bi-{{ $absensi->employee && $absensi->employee->role == 'mandor' ? 'person-badge' : 'building' }} me-1"></i>
                                        {{ ucfirst($absensi->employee->role ?? 'karyawan') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="fw-bold text-dark">{{ $absensi->tanggal->format('d') }}</span>
                                        <small class="text-muted">{{ $absensi->tanggal->format('M Y') }}</small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $absensi->status == 'full' ? 'success' : 'warning' }} px-3 py-2">
                                        <i class="bi bi-{{ $absensi->status == 'full' ? 'check-circle' : 'clock' }} me-1"></i>
                                        {{ $absensi->status_label }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route(auth()->user()->isManager() ? 'manager.absensis.show' : 'admin.absensis.show', $absensi) }}"
                                           class="btn btn-outline-info btn-sm" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route(auth()->user()->isManager() ? 'manager.absensis.edit' : 'admin.absensis.edit', $absensi) }}"
                                           class="btn btn-outline-warning btn-sm" title="Edit Data">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if(auth()->user()->isManager())
                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="confirmDelete({{ $absensi->id }}, '{{ $absensi->employee->nama ?? 'Karyawan Tidak Ditemukan' }}')" title="Hapus Data">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Clean Results Info -->
            <div class="mt-4 text-center">
                <div class="alert alert-light border-0" style="background: linear-gradient(135deg, #f8fafc, #f1f5f9); border-radius: var(--radius-lg);">
                    <div class="d-flex justify-content-center align-items-center">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        <span class="text-muted">
                            Menampilkan <strong>{{ $absensis->count() }}</strong> data absensi
                        </span>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info" role="alert">
                Tidak ada data absensi yang ditemukan.
            </div>
        @endif
    </div>
</div>

<!-- Enhanced Statistics Cards -->
<div class="row mt-4">
    <div class="col-md-4 mb-3">
        <div class="stats-card">
            <div class="stats-icon primary">
                <i class="bi bi-calendar-check"></i>
            </div>
            <h6 class="text-muted mb-1">Total Absensi</h6>
            <h3 class="mb-0 fw-bold text-primary">{{ \App\Models\Absensi::count() }}</h3>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="stats-card">
            <div class="stats-icon success">
                <i class="bi bi-check-circle"></i>
            </div>
            <h6 class="text-muted mb-1">Full Day</h6>
            <h3 class="mb-0 fw-bold text-success">{{ \App\Models\Absensi::where('status', 'full')->count() }}</h3>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="stats-card">
            <div class="stats-icon warning">
                <i class="bi bi-clock"></i>
            </div>
            <h6 class="text-muted mb-1">Setengah Hari</h6>
            <h3 class="mb-0 fw-bold text-warning">{{ \App\Models\Absensi::where('status', 'setengah_hari')->count() }}</h3>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@if(auth()->user()->isManager())
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus absensi tanggal <strong id="absensiName"></strong>?</p>
                <p class="text-danger"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    function confirmDelete(id, name) {
        document.getElementById('absensiName').textContent = name;
        document.getElementById('deleteForm').action = '{{ route("manager.absensis.destroy", ":id") }}'.replace(':id', id);
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
</script>
@endpush
