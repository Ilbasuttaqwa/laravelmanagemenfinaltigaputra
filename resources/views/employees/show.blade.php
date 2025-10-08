@extends('layouts.app')

@section('title', 'Detail Karyawan')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-person-lines-fill"></i>
        Detail Karyawan
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route(auth()->user()->isAdmin() ? 'admin.employees.edit' : 'manager.employees.edit', $employee) }}" 
               class="btn btn-warning">
                <i class="bi bi-pencil"></i>
                Edit
            </a>
            @if(auth()->user()->isAdmin())
                <button type="button" class="btn btn-danger" 
                        onclick="confirmDelete({{ $employee->id }}, '{{ $employee->nama }}')">
                    <i class="bi bi-trash"></i>
                    Hapus
                </button>
            @endif
        </div>
        <a href="{{ route(auth()->user()->isAdmin() ? 'admin.employees.index' : 'manager.employees.index') }}" 
           class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i>
            Kembali
        </a>
    </div>
</div>

<div class="row">
    <!-- Employee Information -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-person-badge"></i>
                    Informasi Karyawan
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <p class="form-control-plaintext">{{ $employee->nama }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Gaji</label>
                        <p class="form-control-plaintext">
                            <span class="h5 text-success">
                                Rp {{ number_format($employee->gaji, 0, ',', '.') }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Summary -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-graph-up"></i>
                    Ringkasan
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 80px; height: 80px;">
                        <span class="text-white h4 fw-bold">{{ substr($employee->nama, 0, 1) }}</span>
                    </div>
                    <h5 class="mt-3 mb-1">{{ $employee->nama }}</h5>
                    <p class="text-muted mb-0">{{ $employee->jabatan }}</p>
                </div>

                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="bi bi-building text-primary"></i> Departemen</span>
                        <span class="badge bg-primary">{{ $employee->departemen }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="bi bi-calendar text-info"></i> Lama Kerja</span>
                        <span class="badge bg-info">{{ $employee->tanggal_masuk->diffForHumans(null, true) }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="bi bi-person text-success"></i> Usia</span>
                        <span class="badge bg-success">{{ $employee->tanggal_lahir->age }} tahun</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="bi bi-check-circle text-{{ $employee->status == 'aktif' ? 'success' : 'secondary' }}"></i> Status</span>
                        <span class="badge bg-{{ $employee->status == 'aktif' ? 'success' : 'secondary' }}">
                            {{ $employee->status == 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning"></i>
                    Aksi Cepat
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route(auth()->user()->isAdmin() ? 'admin.employees.edit' : 'manager.employees.edit', $employee) }}" 
                       class="btn btn-warning">
                        <i class="bi bi-pencil"></i>
                        Edit Data
                    </a>
                    <a href="mailto:{{ $employee->email }}" class="btn btn-info">
                        <i class="bi bi-envelope"></i>
                        Kirim Email
                    </a>
                    <a href="tel:{{ $employee->telepon }}" class="btn btn-success">
                        <i class="bi bi-telephone"></i>
                        Hubungi
                    </a>
                    @if(auth()->user()->isAdmin())
                        <button type="button" class="btn btn-danger" 
                                onclick="confirmDelete({{ $employee->id }}, '{{ $employee->nama }}')">
                            <i class="bi bi-trash"></i>
                            Hapus Karyawan
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@if(auth()->user()->isAdmin())
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus karyawan <strong id="employeeName"></strong>?</p>
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
        document.getElementById('employeeName').textContent = name;
        document.getElementById('deleteForm').action = '{{ route("admin.employees.destroy", ":id") }}'.replace(':id', id);
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
</script>
@endpush
