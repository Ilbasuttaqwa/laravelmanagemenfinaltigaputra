@extends('layouts.app')

@section('title', 'Tambah Karyawan')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-person-plus"></i>
        Tambah Karyawan
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
                <h5 class="card-title mb-0">Form Tambah Karyawan</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route(auth()->user()->isAdmin() ? 'admin.employees.store' : 'manager.employees.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" 
                                   id="nama" name="nama" value="{{ old('nama') }}" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="gaji" class="form-label">Gaji <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('gaji') is-invalid @enderror" 
                                       id="gaji" name="gaji" value="{{ old('gaji') }}" min="0" step="1000" required>
                            </div>
                            @error('gaji')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    @if(auth()->user()->isManager())
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="">Pilih Role</option>
                                <option value="karyawan" {{ old('role') == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                                <option value="mandor" {{ old('role') == 'mandor' ? 'selected' : '' }}>Mandor</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    @else
                    <!-- Hidden field for admin users - always set to karyawan -->
                    <input type="hidden" name="role" value="karyawan">
                    @endif

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route(auth()->user()->isAdmin() ? 'admin.employees.index' : 'manager.employees.index') }}" 
                           class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i>
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
