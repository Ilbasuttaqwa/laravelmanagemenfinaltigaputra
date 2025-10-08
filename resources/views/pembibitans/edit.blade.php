@extends('layouts.app')

@section('title', 'Edit Pembibitan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-pencil"></i> Edit Pembibitan</h1>
    <a href="{{ route(auth()->user()->isManager() ? 'manager.pembibitans.index' : 'admin.pembibitans.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h5 class="card-title mb-0">Form Edit Pembibitan</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route(auth()->user()->isManager() ? 'manager.pembibitans.update' : 'admin.pembibitans.update', $pembibitan) }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nama_pembibitan" class="form-label">Nama Pembibitan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama_pembibitan') is-invalid @enderror"
                           id="nama_pembibitan" name="nama_pembibitan" value="{{ old('nama_pembibitan', $pembibitan->nama_pembibitan) }}" required>
                    @error('nama_pembibitan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-control @error('status') is-invalid @enderror"
                            id="status" name="status" required>
                        <option value="">Pilih Status</option>
                        <option value="aktif" {{ old('status', $pembibitan->status) == 'aktif' ? 'selected' : '' }}>
                            Aktif
                        </option>
                        <option value="non_aktif" {{ old('status', $pembibitan->status) == 'non_aktif' ? 'selected' : '' }}>
                            Non Aktif
                        </option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="kapasitas" class="form-label">Kapasitas</label>
                    <input type="number" class="form-control @error('kapasitas') is-invalid @enderror"
                           id="kapasitas" name="kapasitas" value="{{ old('kapasitas', $pembibitan->kapasitas) }}" min="1">
                    @error('kapasitas')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="luas_lahan" class="form-label">Luas Lahan (m²)</label>
                    <input type="number" class="form-control @error('luas_lahan') is-invalid @enderror"
                           id="luas_lahan" name="luas_lahan" value="{{ old('luas_lahan', $pembibitan->luas_lahan) }}" min="0" step="0.01">
                    @error('luas_lahan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                          id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $pembibitan->deskripsi) }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route(auth()->user()->isManager() ? 'manager.pembibitans.index' : 'admin.pembibitans.index') }}"
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
