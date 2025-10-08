@extends('layouts.app')

@section('title', 'Detail Mandor')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-eye"></i> Detail Mandor</h1>
    <a href="{{ route(auth()->user()->isManager() ? 'manager.mandors.index' : 'admin.mandors.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h5 class="card-title mb-0">Informasi Mandor</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Nama Mandor</label>
                <p class="form-control-plaintext">{{ $mandor->nama }}</p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Gaji</label>
                <p class="form-control-plaintext">
                    <span class="h5 text-success">
                        Rp {{ number_format($mandor->gaji, 0, ',', '.') }}
                    </span>
                </p>
            </div>
        </div>

        <hr>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route(auth()->user()->isManager() ? 'manager.mandors.index' : 'admin.mandors.index') }}"
               class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <a href="{{ route(auth()->user()->isManager() ? 'manager.mandors.edit' : 'admin.mandors.edit', $mandor) }}"
               class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
        </div>
    </div>
</div>
@endsection
