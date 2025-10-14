@extends('layouts.app')

@section('title', 'Master Gudang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-warehouse"></i> Master Gudang</h1>
    <a href="{{ route(auth()->user()->isManager() ? 'manager.gudangs.create' : 'admin.gudangs.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Gudang
    </a>
</div>

<!-- Search Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route(auth()->user()->isManager() ? 'manager.gudangs.index' : 'admin.gudangs.index') }}">
            <div class="row g-3">
                <div class="col-md-8">
                    <label for="search" class="form-label">Cari Gudang</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="{{ request('search') }}" placeholder="Masukkan nama gudang">
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Gudangs Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Daftar Gudang</h5>
    </div>
    <div class="card-body">
        @if($gudangs->count() > 0)
            <div class="table-responsive" style="max-height: 500px; overflow-y: auto; overflow-x: auto;">
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
                        @foreach($gudangs as $index => $gudang)
                            <tr>
                                <td>{{ $gudangs->firstItem() + $index }}</td>
                                <td>{{ $gudang->nama }}</td>
                                <td>Rp {{ number_format($gudang->gaji, 0, ',', '.') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route(auth()->user()->isManager() ? 'manager.gudangs.show' : 'admin.gudangs.show', $gudang) }}"
                                           class="btn btn-info btn-sm" title="Lihat">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route(auth()->user()->isManager() ? 'manager.gudangs.edit' : 'admin.gudangs.edit', $gudang) }}"
                                           class="btn btn-warning btn-sm" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if(auth()->user()->isManager())
                                            <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="confirmDelete({{ $gudang->id }}, '{{ $gudang->nama }}')" title="Hapus">
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
            <!-- Results Info -->
            <div class="d-flex justify-content-center mt-3">
                <small class="text-muted">Menampilkan {{ $gudangs->count() }} dari {{ $gudangs->total() }} data gudang</small>
            </div>
        @else
            <div class="alert alert-info" role="alert">
                Tidak ada data gudang yang ditemukan.
            </div>
        @endif
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mt-4">
    <div class="col-md-6 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total Karyawan Gudang</h6>
                        <h4 class="mb-0">{{ \App\Models\Gudang::count() }}</h4>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-warehouse-fill fa-2x"></i>
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
                        <h4 class="mb-0">Rp {{ number_format(\App\Models\Gudang::sum('gaji'), 0, ',', '.') }}</h4>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-currency-dollar fa-2x"></i>
                    </div>
                </div>
            </div>
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
                <p>Apakah Anda yakin ingin menghapus gudang <strong id="gudangName"></strong>?</p>
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
        document.getElementById('gudangName').textContent = name;
        document.getElementById('deleteForm').action = '{{ route("manager.gudangs.destroy", ":id") }}'.replace(':id', id);
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
</script>
@endpush
