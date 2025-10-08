@extends('layouts.app')

@section('title', 'Edit Gudang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-pencil"></i> Edit Gudang</h1>
    <a href="{{ route(auth()->user()->isManager() ? 'manager.gudangs.index' : 'admin.gudangs.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h5 class="card-title mb-0">Form Edit Gudang</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route(auth()->user()->isManager() ? 'manager.gudangs.update' : 'admin.gudangs.update', $gudang) }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nama" class="form-label">Nama Gudang <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama') is-invalid @enderror"
                           id="nama" name="nama" value="{{ old('nama', $gudang->nama) }}" required>
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="gaji" class="form-label">Gaji <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control @error('gaji') is-invalid @enderror"
                               id="gaji" name="gaji" value="{{ old('gaji', $gudang->gaji) }}" min="0" step="1000" required>
                    </div>
                    @error('gaji')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route(auth()->user()->isManager() ? 'manager.gudangs.index' : 'admin.gudangs.index') }}"
                   class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
