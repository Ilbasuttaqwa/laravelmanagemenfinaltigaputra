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
                    {{ $absensi->employee->nama ?? 'Karyawan Tidak Ditemukan' }}
                </p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Role</label>
                <p class="form-control-plaintext">
                    <i class="bi bi-person-badge me-2"></i>
                    {{ ucfirst($absensi->employee->role ?? 'karyawan') }}
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Tanggal</label>
                <p class="form-control-plaintext">
                    <i class="bi bi-calendar me-2"></i>
                    {{ $absensi->tanggal->format('d F Y') }}
                </p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Status Kehadiran</label>
                <p class="form-control-plaintext">
                    @if($absensi->status == 'full')
                        <span class="text-success"><i class="bi bi-check-circle me-2"></i>Full Day</span>
                    @else
                        <span class="text-warning"><i class="bi bi-clock me-2"></i>Half Day</span>
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