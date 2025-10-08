@extends('layouts.app')

@section('title', 'Master Karyawan')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-people"></i>
        Master Karyawan
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route(auth()->user()->isAdmin() ? 'admin.employees.create' : 'manager.employees.create') }}" 
           class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            Tambah Karyawan
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total Karyawan</h6>
                        <h4 class="mb-0">{{ \App\Models\Employee::count() }}</h4>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-people-fill fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total Gaji</h6>
                        <h4 class="mb-0">Rp {{ number_format(\App\Models\Employee::sum('gaji'), 0, ',', '.') }}</h4>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-currency-dollar fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route(auth()->user()->isAdmin() ? 'admin.employees.index' : 'manager.employees.index') }}">
            <div class="row g-3">
                <div class="col-md-8">
                    <label for="search" class="form-label">Cari Karyawan</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Masukkan nama karyawan">
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                            Cari
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Employees Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Daftar Karyawan</h5>
    </div>
    <div class="card-body">
        @if($employees->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Gaji</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $index => $employee)
                            <tr>
                                <td>{{ $employees->firstItem() + $index }}</td>
                                <td>{{ $employee->nama }}</td>
                                <td>Rp {{ number_format($employee->gaji, 0, ',', '.') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route(auth()->user()->isManager() ? 'manager.employees.show' : 'admin.employees.show', $employee) }}" 
                                           class="btn btn-info btn-sm" title="Lihat">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route(auth()->user()->isManager() ? 'manager.employees.edit' : 'admin.employees.edit', $employee) }}" 
                                           class="btn btn-warning btn-sm" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if(auth()->user()->isManager())
                                            <button type="button" class="btn btn-danger btn-sm" 
                                                    onclick="confirmDelete({{ $employee->id }}, '{{ $employee->nama }}')" title="Hapus">
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

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $employees->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                <h5 class="text-muted mt-3">Tidak ada data karyawan</h5>
                <p class="text-muted">Mulai dengan menambahkan karyawan baru.</p>
                <a href="{{ route(auth()->user()->isAdmin() ? 'admin.employees.create' : 'manager.employees.create') }}" 
                   class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i>
                    Tambah Karyawan Pertama
                </a>
            </div>
        @endif
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
        @if(auth()->user()->isManager())
            document.getElementById('deleteForm').action = '{{ route("manager.employees.destroy", ":id") }}'.replace(':id', id);
        @else
            document.getElementById('deleteForm').action = '{{ route("admin.employees.destroy", ":id") }}'.replace(':id', id);
        @endif
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
</script>
@endpush
