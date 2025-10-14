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
                <label class="form-label fw-bold">Judul Pembibitan</label>
                <p class="form-control-plaintext">
                    <i class="bi bi-seedling me-2"></i>
                    {{ $pembibitan->judul }}
                </p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Tanggal Mulai</label>
                <p class="form-control-plaintext">
                    <i class="bi bi-calendar me-2"></i>
                    {{ $pembibitan->tanggal_mulai->format('d/m/Y') }}
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Lokasi</label>
                <p class="form-control-plaintext">
                    @if($pembibitan->lokasi)
                        <i class="bi bi-geo-alt me-2"></i>
                        {{ $pembibitan->lokasi->nama_lokasi }}
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Kandang</label>
                <p class="form-control-plaintext">
                    @if($pembibitan->kandang)
                        <i class="bi bi-house me-2"></i>
                        {{ $pembibitan->kandang->nama_kandang }}
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </p>
            </div>
        </div>

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
