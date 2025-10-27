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
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Tidak ada data karyawan kandang yang ditemukan.
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
