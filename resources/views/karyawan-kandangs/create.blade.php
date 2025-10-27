@extends('layouts.app')

@section('title', 'Tambah Karyawan Kandang')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Tambah Karyawan Kandang</h1>
        <a href="{{ route(auth()->user()->isAdmin() ? 'admin.karyawan-kandangs.index' : 'manager.karyawan-kandangs.index') }}"
           class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i>
            Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Form Tambah Karyawan Kandang</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route(auth()->user()->isAdmin() ? 'admin.karyawan-kandangs.store' : 'manager.karyawan-kandangs.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Karyawan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama') is-invalid @enderror"
                           id="nama" name="nama" value="{{ old('nama') }}" required>
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="gaji_pokok" class="form-label">Gaji Pokok <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('gaji_pokok') is-invalid @enderror"
                           id="gaji_pokok" name="gaji_pokok" value="{{ old('gaji_pokok') }}" 
                           min="0" step="1000" required>
                    @error('gaji_pokok')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route(auth()->user()->isAdmin() ? 'admin.karyawan-kandangs.index' : 'manager.karyawan-kandangs.index') }}"
                       class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i>
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
