@extends('layouts.app')

@section('title', 'Detail Absensi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-eye"></i> Detail Absensi</h1>
    <a href="{{ route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h5 class="card-title mb-0">Informasi Absensi</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Nama Karyawan</label>
                <p class="form-control-plaintext">
                    <i class="bi bi-person me-2"></i>
                    {{ $absensi->nama }}
                </p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Gaji</label>
                <p class="form-control-plaintext">
                    <i class="bi bi-currency-dollar me-2"></i>
                    {{ $absensi->gaji_formatted }}
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Keterangan</label>
                <p class="form-control-plaintext">
                    <span class="badge {{ $absensi->keterangan_badge }} fs-6">
                        {{ $absensi->keterangan_label }}
                    </span>
                </p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Status Kehadiran</label>
                <p class="form-control-plaintext">
                    @if($absensi->keterangan == 'masuk_full')
                        <span class="text-success"><i class="bi bi-check-circle me-2"></i>Full Day (True)</span>
                    @else
                        <span class="text-warning"><i class="bi bi-clock me-2"></i>Half Day (False)</span>
                    @endif
                </p>
            </div>
        </div>

        <hr>

        <h6 class="fw-bold mb-3"><i class="bi bi-building me-2"></i>Informasi Master Data</h6>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Gudang</label>
                <p class="form-control-plaintext">
                    @if($absensi->gudang)
                        <i class="bi bi-warehouse me-2"></i>{{ $absensi->gudang->nama }}
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Mandor</label>
                <p class="form-control-plaintext">
                    @if($absensi->mandor)
                        <i class="bi bi-person-badge me-2"></i>{{ $absensi->mandor->nama }}
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Kandang</label>
                <p class="form-control-plaintext">
                    @if($absensi->kandang)
                        <i class="bi bi-house me-2"></i>{{ $absensi->kandang->nama_kandang }}
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Lokasi</label>
                <p class="form-control-plaintext">
                    @if($absensi->lokasi)
                        <i class="bi bi-geo-alt me-2"></i>{{ $absensi->lokasi->nama_lokasi }}
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Pembibitan</label>
                <p class="form-control-plaintext">
                    @if($absensi->pembibitan)
                        <i class="bi bi-seedling me-2"></i>{{ $absensi->pembibitan->judul }}
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </p>
            </div>
        </div>

        <hr>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index') }}"
               class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <a href="{{ route(auth()->user()->isManager() ? 'manager.absensis.edit' : 'admin.absensis.edit', $absensi) }}"
               class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
        </div>
    </div>
</div>
@endsection