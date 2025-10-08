@extends('layouts.app')

@section('title', 'Edit Karyawan')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-person-gear"></i>
        Edit Karyawan
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route(auth()->user()->isAdmin() ? 'admin.employees.index' : 'manager.employees.index') }}" 
           class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i>
            Kembali
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Form Edit Karyawan - {{ $employee->nama }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route(auth()->user()->isAdmin() ? 'admin.employees.update' : 'manager.employees.update', $employee) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" 
                                   id="nama" name="nama" value="{{ old('nama', $employee->nama) }}" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="gaji" class="form-label">Gaji <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('gaji') is-invalid @enderror" 
                                       id="gaji" name="gaji" value="{{ old('gaji', $employee->gaji) }}" min="0" step="1000" required>
                            </div>
                            @error('gaji')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route(auth()->user()->isAdmin() ? 'admin.employees.index' : 'manager.employees.index') }}" 
                           class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i>
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i>
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
