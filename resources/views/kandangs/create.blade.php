@extends('layouts.app')

@section('title', 'Tambah Kandang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-house-plus"></i>
        Tambah Kandang
    </h1>
    <a href="{{ route(auth()->user()->isAdmin() ? 'admin.kandangs.index' : 'manager.kandangs.index') }}" 
       class="btn btn-secondary">
        <i class="bi bi-arrow-left-circle"></i>
        Kembali
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Form Tambah Kandang</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route(auth()->user()->isAdmin() ? 'admin.kandangs.store' : 'manager.kandangs.store') }}">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nama_kandang" class="form-label">Nama Kandang <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama_kandang') is-invalid @enderror"
                           id="nama_kandang" name="nama_kandang" value="{{ old('nama_kandang') }}" required>
                    @error('nama_kandang')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="lokasi_id" class="form-label">Lokasi <span class="text-danger">*</span></label>
                    <select class="form-select @error('lokasi_id') is-invalid @enderror" id="lokasi_id" name="lokasi_id" required>
                        <option value="">Pilih Lokasi</option>
                        @foreach($lokasis as $lokasi)
                            <option value="{{ $lokasi->id }}" {{ old('lokasi_id') == $lokasi->id ? 'selected' : '' }}>
                                {{ $lokasi->nama_lokasi }}
                            </option>
                        @endforeach
                    </select>
                    @error('lokasi_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>


            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                          id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route(auth()->user()->isAdmin() ? 'admin.kandangs.index' : 'manager.kandangs.index') }}"
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

@endsection
