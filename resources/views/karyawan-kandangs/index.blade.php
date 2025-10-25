@extends('layouts.app')

@section('title', 'Master Karyawan Kandang')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-people-fill"></i>
            Master Karyawan Kandang
        </h1>
        <a href="{{ route(auth()->user()->isAdmin() ? 'admin.karyawan-kandangs.create' : 'manager.karyawan-kandangs.create') }}"
           class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            Tambah Karyawan Kandang
        </a>
    </div>

    <!-- Search Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route(auth()->user()->isAdmin() ? 'admin.karyawan-kandangs.index' : 'manager.karyawan-kandangs.index') }}">
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

    <!-- Karyawan Kandang Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Daftar Karyawan Kandang</h5>
        </div>
        <div class="card-body">
            @if($karyawans->count() > 0)
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto; overflow-x: auto;">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Karyawan</th>
                                <th>Gaji Pokok</th>
                                <th>Kandang</th>
                                <th>Lokasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($karyawans as $index => $karyawan)
                                <tr>
                                    <td>{{ $karyawans->firstItem() + $index }}</td>
                                    <td>
                                        <strong>{{ $karyawan->nama }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Rp {{ number_format($karyawan->gaji_pokok, 0, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        @if($karyawan->kandang)
                                            <span class="badge bg-info">{{ $karyawan->kandang->nama_kandang }}</span>
                                        @else
                                            <span class="badge bg-secondary">Belum ada</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($karyawan->kandang && $karyawan->kandang->lokasi)
                                            <span class="badge bg-primary">{{ $karyawan->kandang->lokasi->nama_lokasi }}</span>
                                        @else
                                            <span class="badge bg-secondary">Belum ada</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route(auth()->user()->isManager() ? 'manager.karyawan-kandangs.show' : 'admin.karyawan-kandangs.show', $karyawan) }}"
                                               class="btn btn-info btn-sm" title="Lihat">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route(auth()->user()->isManager() ? 'manager.karyawan-kandangs.edit' : 'admin.karyawan-kandangs.edit', $karyawan) }}"
                                               class="btn btn-warning btn-sm" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @if(auth()->user()->isManager())
                                                <form action="{{ route('manager.karyawan-kandangs.destroy', $karyawan) }}" method="POST" 
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus karyawan ini?')" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Menampilkan {{ $karyawans->firstItem() }} sampai {{ $karyawans->lastItem() }} dari {{ $karyawans->total() }} data
                    </div>
                    <div>
                        {{ $karyawans->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-people display-1 text-muted"></i>
                    <h5 class="text-muted mt-3">Belum ada data karyawan kandang</h5>
                    <p class="text-muted">Silakan tambah data karyawan kandang terlebih dahulu.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
