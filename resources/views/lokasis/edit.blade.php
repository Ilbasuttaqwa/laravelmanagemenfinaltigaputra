@extends('layouts.app')

@section('title', 'Edit Lokasi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Lokasi</h1>
        <a href="{{ route(auth()->user()->isAdmin() ? 'admin.lokasis.index' : 'manager.lokasis.index') }}"
           class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i>
            Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Form Edit Lokasi</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route(auth()->user()->isAdmin() ? 'admin.lokasis.update' : 'manager.lokasis.update', $lokasi) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="nama_lokasi" class="form-label">Nama Lokasi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama_lokasi') is-invalid @enderror"
                           id="nama_lokasi" name="nama_lokasi" value="{{ old('nama_lokasi', $lokasi->nama_lokasi) }}" required>
                    @error('nama_lokasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route(auth()->user()->isAdmin() ? 'admin.lokasis.index' : 'manager.lokasis.index') }}"
                       class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i>
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i>
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
