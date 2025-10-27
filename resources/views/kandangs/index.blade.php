@extends('layouts.app')

@section('title', 'Master Kandang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-house"></i>
        Master Kandang
    </h1>
    <a href="{{ route(auth()->user()->isAdmin() ? 'admin.kandangs.create' : 'manager.kandangs.create') }}" 
       class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        Tambah Kandang
    </a>
</div>


<!-- Kandangs Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Daftar Kandang</h5>
    </div>
    <div class="card-body">
        @if($kandangs->count() > 0)
            <div class="table-responsive" style="max-height: 500px; overflow-y: auto; overflow-x: auto;">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Kandang</th>
                            <th>Lokasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kandangs as $index => $kandang)
                            <tr>
                                <td>{{ $kandangs->firstItem() + $index }}</td>
                                <td>{{ $kandang->nama_kandang }}</td>
                                <td>
                                    @if($kandang->lokasi)
                                        <span class="badge bg-info">{{ $kandang->lokasi->nama_lokasi }}</span>
                                    @else
                                        <span class="badge bg-secondary">Belum ada</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route(auth()->user()->isManager() ? 'manager.kandangs.show' : 'admin.kandangs.show', $kandang) }}"
                                           class="btn btn-info btn-sm" title="Lihat">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route(auth()->user()->isManager() ? 'manager.kandangs.edit' : 'admin.kandangs.edit', $kandang) }}"
                                           class="btn btn-warning btn-sm" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                            @if(auth()->user()->isManager())
                                                <button type="button" class="btn btn-danger btn-sm"
                                                        data-item-id="{{ $kandang->id }}" data-item-name="{{ $kandang->nama_kandang }}" onclick="confirmDelete(this)" title="Hapus">
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
                <small class="text-muted">Menampilkan {{ $kandangs->count() }} dari {{ $kandangs->total() }} data kandang</small>
            </div>
        @else
            <div class="alert alert-info" role="alert">
                Tidak ada data kandang yang ditemukan.
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
    function confirmDelete(button) {
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
