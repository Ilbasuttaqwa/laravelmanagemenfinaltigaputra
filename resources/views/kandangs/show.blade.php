@extends('layouts.app')

@section('title', 'Detail Kandang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-house-check"></i>
        Detail Kandang
    </h1>
    <div>
        <a href="{{ route(auth()->user()->isAdmin() ? 'admin.kandangs.edit' : 'manager.kandangs.edit', $kandang) }}" 
           class="btn btn-warning me-2">
            <i class="bi bi-pencil"></i>
            Edit
        </a>
        <a href="{{ route(auth()->user()->isAdmin() ? 'admin.kandangs.index' : 'manager.kandangs.index') }}" 
           class="btn btn-secondary">
            <i class="bi bi-arrow-left-circle"></i>
            Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informasi Kandang</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Nama Kandang</label>
                        <p class="form-control-plaintext">{{ $kandang->nama_kandang }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Lokasi</label>
                        <p class="form-control-plaintext">
                            @if($kandang->lokasi)
                                <span class="badge bg-info fs-6">{{ $kandang->lokasi->nama_lokasi }}</span>
                            @else
                                <span class="badge bg-secondary fs-6">Belum ada</span>
                            @endif
                        </p>
                    </div>
                </div>


                @if($kandang->deskripsi)
                <div class="mb-3">
                    <label class="form-label fw-bold">Deskripsi</label>
                    <p class="form-control-plaintext">{{ $kandang->deskripsi }}</p>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Dibuat</label>
                        <p class="form-control-plaintext">{{ $kandang->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Diperbarui</label>
                        <p class="form-control-plaintext">{{ $kandang->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Statistik</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <i class="bi bi-house-fill fa-4x text-primary"></i>
                </div>
                
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="bi bi-house me-2"></i>Nama Kandang</span>
                        <span class="badge bg-primary rounded-pill">{{ $kandang->nama_kandang }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="bi bi-geo-alt me-2"></i>Lokasi</span>
                        @if($kandang->lokasi)
                            <span class="badge bg-info rounded-pill">{{ $kandang->lokasi->nama_lokasi }}</span>
                        @else
                            <span class="badge bg-secondary rounded-pill">Belum ada</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if(auth()->user()->isAdmin())
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Aksi</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.kandangs.edit', $kandang) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i>
                        Edit Kandang
                    </a>
                    <button type="button" class="btn btn-danger" 
                            onclick="confirmDelete({{ $kandang->id }}, '{{ $kandang->nama_kandang }}')">
                        <i class="bi bi-trash"></i>
                        Hapus Kandang
                    </button>
                </div>
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
                Apakah Anda yakin ingin menghapus kandang <strong id="kandangNameToDelete"></strong>?
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
    function confirmDelete(kandangId, kandangName) {
        const deleteForm = document.getElementById('deleteForm');
        const kandangNameToDelete = document.getElementById('kandangNameToDelete');
        const baseUrl = "{{ url('/') }}";
        const rolePrefix = "{{ auth()->user()->isAdmin() ? 'admin' : 'manager' }}";
        
        deleteForm.action = `${baseUrl}/${rolePrefix}/kandangs/${kandangId}`;
        kandangNameToDelete.textContent = kandangName;
        
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
        deleteModal.show();
    }
</script>
@endsection
