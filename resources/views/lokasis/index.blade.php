@extends('layouts.app')

@section('title', 'Master Lokasi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-geo-alt"></i>
            Master Lokasi
        </h1>
        <a href="{{ route(auth()->user()->isAdmin() ? 'admin.lokasis.create' : 'manager.lokasis.create') }}"
           class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            Tambah Lokasi
        </a>
    </div>

    <!-- Search Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route(auth()->user()->isAdmin() ? 'admin.lokasis.index' : 'manager.lokasis.index') }}">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label for="search" class="form-label">Cari Lokasi</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}" placeholder="Masukkan nama lokasi atau deskripsi">
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

    <!-- Lokasis Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Daftar Lokasi</h5>
        </div>
        <div class="card-body">
            @if($lokasis->count() > 0)
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto; overflow-x: auto;">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Lokasi</th>
                                <th>Deskripsi</th>
                                <th>Jumlah Kandang</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lokasis as $index => $lokasi)
                                <tr>
                                    <td>{{ $lokasis->firstItem() + $index }}</td>
                                    <td>
                                        <strong>{{ $lokasi->nama_lokasi }}</strong>
                                    </td>
                                    <td>
                                        @if($lokasi->deskripsi)
                                            {{ Str::limit($lokasi->deskripsi, 50) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $lokasi->kandangs_count }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route(auth()->user()->isManager() ? 'manager.lokasis.show' : 'admin.lokasis.show', $lokasi) }}"
                                               class="btn btn-info btn-sm" title="Lihat">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route(auth()->user()->isManager() ? 'manager.lokasis.edit' : 'admin.lokasis.edit', $lokasi) }}"
                                               class="btn btn-warning btn-sm" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @if(auth()->user()->isManager())
                                                <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="confirmDelete({{ $lokasi->id }}, '{{ $lokasi->nama_lokasi }}')" title="Hapus">
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
                    {{ $lokasis->links() }}
                </div>
            @else
                <div class="alert alert-info" role="alert">
                    Tidak ada data lokasi yang ditemukan.
                </div>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mt-4">
        <div class="col-md-4 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Lokasi</h6>
                            <h4 class="mb-0">{{ \App\Models\Lokasi::count() }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-geo-alt-fill fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Lokasi Terpakai</h6>
                            <h4 class="mb-0">{{ \App\Models\Lokasi::has('kandangs')->count() }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-geo-alt-check fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Kandang</h6>
                            <h4 class="mb-0">{{ \App\Models\Kandang::count() }}</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-house-door fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus lokasi <strong id="lokasiNameToDelete"></strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(lokasiId, lokasiName) {
            const deleteForm = document.getElementById('deleteForm');
            const lokasiNameToDelete = document.getElementById('lokasiNameToDelete');
            const baseUrl = "{{ url('/') }}";
            const rolePrefix = "{{ auth()->user()->isAdmin() ? 'admin' : 'manager' }}";
            deleteForm.action = `${baseUrl}/${rolePrefix}/lokasis/${lokasiId}`;
            lokasiNameToDelete.textContent = lokasiName;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            deleteModal.show();
        }
    </script>
@endsection
