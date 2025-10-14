@extends('layouts.app')

@section('title', 'Master Pembibitan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-seedling"></i> Master Pembibitan</h1>
    <a href="{{ route(auth()->user()->isManager() ? 'manager.pembibitans.create' : 'admin.pembibitans.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Pembibitan
    </a>
</div>

<!-- Search Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route(auth()->user()->isManager() ? 'manager.pembibitans.index' : 'admin.pembibitans.index') }}">
            <div class="row g-3">
                <div class="col-md-8">
                    <label for="search" class="form-label">Cari Pembibitan</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="{{ request('search') }}" placeholder="Judul, lokasi, atau kandang">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid gap-2">
                        <a href="{{ route(auth()->user()->isManager() ? 'manager.pembibitans.index' : 'admin.pembibitans.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Pembibitans Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Daftar Pembibitan</h5>
    </div>
    <div class="card-body">
        @if($pembibitans->count() > 0)
            <div class="table-responsive" style="max-height: 500px; overflow-y: auto; overflow-x: auto;">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
                            <th>Lokasi</th>
                            <th>Kandang</th>
                            <th>Tanggal Mulai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pembibitans as $index => $pembibitan)
                            <tr>
                                <td>{{ ($pembibitans->currentPage() - 1) * $pembibitans->perPage() + $index + 1 }}</td>
                                <td>
                                    <strong>{{ $pembibitan->judul }}</strong>
                                </td>
                                <td>
                                    @if($pembibitan->lokasi)
                                        {{ $pembibitan->lokasi->nama_lokasi }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($pembibitan->kandang)
                                        {{ $pembibitan->kandang->nama_kandang }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $pembibitan->tanggal_mulai->format('d/m/Y') }}
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route(auth()->user()->isManager() ? 'manager.pembibitans.show' : 'admin.pembibitans.show', $pembibitan) }}"
                                           class="btn btn-info btn-sm" title="Lihat">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route(auth()->user()->isManager() ? 'manager.pembibitans.edit' : 'admin.pembibitans.edit', $pembibitan) }}"
                                           class="btn btn-warning btn-sm" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if(auth()->user()->isManager())
                                        <button type="button" class="btn btn-danger btn-sm"
                                                onclick="confirmDelete({{ $pembibitan->id }}, '{{ $pembibitan->judul }}')" title="Hapus">
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
                {{ $pembibitans->links() }}
            </div>
        @else
            <div class="alert alert-info" role="alert">
                Tidak ada data pembibitan yang ditemukan.
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
                        <h6 class="card-title">Total Pembibitan</h6>
                        <h4 class="mb-0">{{ \App\Models\Pembibitan::count() }}</h4>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-seedling fa-2x"></i>
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
                        <h6 class="card-title">Pembibitan Berjalan</h6>
                        <h4 class="mb-0">{{ \App\Models\Pembibitan::where('tanggal_mulai', '<=', now())->count() }}</h4>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-calendar-check-fill fa-2x"></i>
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
                <p>Apakah Anda yakin ingin menghapus pembibitan <strong id="pembibitanName"></strong>?</p>
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
        document.getElementById('pembibitanName').textContent = name;
        document.getElementById('deleteForm').action = '{{ route("manager.pembibitans.destroy", ":id") }}'.replace(':id', id);
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
</script>
@endpush
