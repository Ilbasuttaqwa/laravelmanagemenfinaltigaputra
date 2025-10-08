@extends('layouts.app')

@section('title', 'Detail Lokasi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Detail Lokasi</h1>
        <a href="{{ route(auth()->user()->isAdmin() ? 'admin.lokasis.index' : 'manager.lokasis.index') }}"
           class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i>
            Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Lokasi</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nama Lokasi</label>
                            <p class="form-control-plaintext">{{ $lokasi->nama_lokasi }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jumlah Kandang</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-primary fs-6">{{ $lokasi->kandangs->count() }} kandang</span>
                            </p>
                        </div>
                    </div>

                    @if($lokasi->deskripsi)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <p class="form-control-plaintext">{{ $lokasi->deskripsi }}</p>
                    </div>
                    @endif

                    <hr>

                    <h6 class="mb-3">Detail Lokasi</h6>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="bi bi-tag me-2"></i>Nama Lokasi</span>
                            <span class="badge bg-primary rounded-pill">{{ $lokasi->nama_lokasi }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="bi bi-house-door me-2"></i>Jumlah Kandang</span>
                            <span class="badge bg-info rounded-pill">{{ $lokasi->kandangs->count() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="bi bi-calendar me-2"></i>Dibuat</span>
                            <span class="text-muted">{{ $lokasi->created_at->format('d M Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="bi bi-arrow-clockwise me-2"></i>Diupdate</span>
                            <span class="text-muted">{{ $lokasi->updated_at->format('d M Y H:i') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Aksi</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route(auth()->user()->isAdmin() ? 'admin.lokasis.edit' : 'manager.lokasis.edit', $lokasi) }}"
                           class="btn btn-warning">
                            <i class="bi bi-pencil"></i>
                            Edit Lokasi
                        </a>
                        @if(auth()->user()->isAdmin())
                            <button type="button" class="btn btn-danger"
                                    onclick="confirmDelete({{ $lokasi->id }}, '{{ $lokasi->nama_lokasi }}')">
                                <i class="bi bi-trash"></i>
                                Hapus Lokasi
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            @if($lokasi->kandangs->count() > 0)
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Kandang di Lokasi Ini</h5>
                </div>
                <div class="card-body">
                    @foreach($lokasi->kandangs as $kandang)
                        <div class="border rounded p-2 mb-2">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $kandang->nama_kandang }}</h6>
                                    @if($kandang->employees->count() > 0)
                                        <div class="small">
                                            <strong>Karyawan:</strong>
                                            @foreach($kandang->employees as $employee)
                                                <span class="badge bg-primary me-1">{{ $employee->nama }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <a href="{{ route(auth()->user()->isAdmin() ? 'admin.kandangs.show' : 'manager.kandangs.show', $kandang) }}"
                                   class="btn btn-sm btn-outline-primary">Lihat</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
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
                    @if($lokasi->kandangs->count() > 0)
                        <div class="alert alert-warning mt-2">
                            <i class="bi bi-exclamation-triangle"></i>
                            Lokasi ini memiliki {{ $lokasi->kandangs->count() }} kandang. 
                            Tidak dapat dihapus jika masih memiliki kandang.
                        </div>
                    @endif
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
</div>
@endsection
