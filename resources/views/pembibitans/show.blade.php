@extends('layouts.app')

@section('title', 'Detail Pembibitan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-eye"></i> Detail Pembibitan</h1>
    <a href="{{ route(auth()->user()->isManager() ? 'manager.pembibitans.index' : 'admin.pembibitans.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h5 class="card-title mb-0">Informasi Pembibitan</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Nama Pembibitan</label>
                <p class="form-control-plaintext">
                    <i class="bi bi-seedling me-2"></i>
                    {{ $pembibitan->nama_pembibitan }}
                </p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Status</label>
                <p class="form-control-plaintext">
                    <span class="badge {{ $pembibitan->status_badge }} fs-6">
                        {{ $pembibitan->status_label }}
                    </span>
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Kapasitas</label>
                <p class="form-control-plaintext">
                    @if($pembibitan->kapasitas)
                        <i class="bi bi-collection me-2"></i>
                        {{ number_format($pembibitan->kapasitas) }}
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Luas Lahan</label>
                <p class="form-control-plaintext">
                    @if($pembibitan->luas_lahan)
                        <i class="bi bi-rulers me-2"></i>
                        {{ number_format($pembibitan->luas_lahan, 2) }} m²
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </p>
            </div>
        </div>

        @if($pembibitan->deskripsi)
        <div class="mb-3">
            <label class="form-label fw-bold">Deskripsi</label>
            <div class="card bg-light">
                <div class="card-body">
                    <p class="mb-0">{{ $pembibitan->deskripsi }}</p>
                </div>
            </div>
        </div>
        @endif

        <hr>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route(auth()->user()->isManager() ? 'manager.pembibitans.index' : 'admin.pembibitans.index') }}"
               class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <a href="{{ route(auth()->user()->isManager() ? 'manager.pembibitans.edit' : 'admin.pembibitans.edit', $pembibitan) }}"
               class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
        </div>
    </div>
</div>
@endsection
