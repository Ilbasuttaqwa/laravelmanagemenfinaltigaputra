@extends('layouts.app')

@section('title', 'Master Mandor')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-person-badge"></i> Master Mandor</h1>
    <a href="{{ route(auth()->user()->isManager() ? 'manager.mandors.create' : 'admin.mandors.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Mandor
    </a>
</div>



<!-- Mandors Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Daftar Mandor</h5>
    </div>
    <div class="card-body">
        @if($mandors->count() > 0)
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
                        @foreach($mandors as $index => $mandor)
                            <tr>
                                <td>{{ $mandors->firstItem() + $index }}</td>
                                <td>{{ $mandor->nama }}</td>
                                <td>Rp {{ number_format($mandor->gaji, 0, ',', '.') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route(auth()->user()->isManager() ? 'manager.mandors.show' : 'admin.mandors.show', $mandor) }}"
                                           class="btn btn-info btn-sm" title="Lihat">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route(auth()->user()->isManager() ? 'manager.mandors.edit' : 'admin.mandors.edit', $mandor) }}"
                                           class="btn btn-warning btn-sm" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if(auth()->user()->isManager())
                                            <button type="button" class="btn btn-danger btn-sm"
                                                    data-item-id="{{ $mandor->id }}" data-item-name="{{ $mandor->nama }}" onclick="confirmDelete(this)" title="Hapus">
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
                {{ $mandors->links() }}
            </div>
        @else
            <div class="alert alert-info" role="alert">
                Tidak ada data mandor yang ditemukan.
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
                <p>Apakah Anda yakin ingin menghapus mandor <strong id="mandorName"></strong>?</p>
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
    function confirmDelete(button) {
        document.getElementById('mandorName').textContent = name;
        document.getElementById('deleteForm').action = '{{ route("manager.mandors.destroy", ":id") }}'.replace(':id', id);
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
</script>
@endpush
