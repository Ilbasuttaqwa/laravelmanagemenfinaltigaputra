@extends('layouts.app')

@section('title', 'Detail Karyawan Kandang')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Detail Karyawan Kandang</h1>
        <a href="{{ route(auth()->user()->isAdmin() ? 'admin.karyawan-kandangs.index' : 'manager.karyawan-kandangs.index') }}"
           class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i>
            Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Karyawan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Karyawan</label>
                                <p class="form-control-plaintext">{{ $karyawanKandang->nama }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Gaji Pokok</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-success fs-6">Rp {{ number_format($karyawanKandang->gaji_pokok, 0, ',', '.') }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Kandang</label>
                                <p class="form-control-plaintext">
                                    @if($karyawanKandang->kandang)
                                        <span class="badge bg-info fs-6">{{ $karyawanKandang->kandang->nama_kandang }}</span>
                                    @else
                                        <span class="text-muted">Belum ada</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Lokasi</label>
                                <p class="form-control-plaintext">
                                    @if($karyawanKandang->kandang && $karyawanKandang->kandang->lokasi)
                                        <span class="badge bg-primary fs-6">{{ $karyawanKandang->kandang->lokasi->nama_lokasi }}</span>
                                    @else
                                        <span class="text-muted">Belum ada</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
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
                        <a href="{{ route(auth()->user()->isManager() ? 'manager.karyawan-kandangs.edit' : 'admin.karyawan-kandangs.edit', $karyawanKandang) }}"
                           class="btn btn-warning">
                            <i class="bi bi-pencil"></i>
                            Edit Karyawan
                        </a>
                        @if(auth()->user()->isManager())
                            <form action="{{ route('manager.karyawan-kandangs.destroy', $karyawanKandang) }}" method="POST" 
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus karyawan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="bi bi-trash"></i>
                                    Hapus Karyawan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
