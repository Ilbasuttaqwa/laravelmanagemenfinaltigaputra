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
                                                        data-id="{{ $lokasi->id }}"
                                                        data-name="{{ $lokasi->nama_lokasi }}"
                                                        data-type="lokasi"
                                                        data-url="{{ route(auth()->user()->isManager() ? 'manager.lokasis.destroy' : 'admin.lokasis.destroy', $lokasi->id) }}"
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

@endsection

@push('scripts')
<script>
// Delete handler is now loaded globally via delete-handler.js
// confirmDelete() function is available globally
console.log('✅ Lokasi index page loaded - Delete handler ready');
</script>
@endpush
