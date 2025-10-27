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
                                                        data-lokasi-id="{{ $lokasi->id }}"
                                                        data-lokasi-name="{{ $lokasi->nama_lokasi }}"
                                                        onclick="confirmDelete(this)" title="Hapus">
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
        function confirmDelete(button) {
            const lokasiId = button.getAttribute('data-lokasi-id');
            const lokasiName = button.getAttribute('data-lokasi-name');
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
