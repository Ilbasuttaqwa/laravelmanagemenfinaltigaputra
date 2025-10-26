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

                    @if(auth()->user()->isManager())
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="">Pilih Role</option>
                                <option value="karyawan" {{ old('role', $employee->role) == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                                <option value="mandor" {{ old('role', $employee->role) == 'mandor' ? 'selected' : '' }}>Mandor</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="kandang_id" class="form-label">Kandang</label>
                            <select class="form-select @error('kandang_id') is-invalid @enderror" id="kandang_id" name="kandang_id">
                                <option value="">Pilih Kandang</option>
                                @foreach(\App\Models\Kandang::with('lokasi')->get() as $kandang)
                                    <option value="{{ $kandang->id }}" {{ old('kandang_id', $employee->kandang_id) == $kandang->id ? 'selected' : '' }}>
                                        {{ $kandang->nama_kandang }} 
                                        @if($kandang->lokasi)
                                            ({{ $kandang->lokasi->nama_lokasi }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('kandang_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lokasi_kerja" class="form-label">Lokasi Kerja</label>
                            <input type="text" class="form-control @error('lokasi_kerja') is-invalid @enderror" 
                                   id="lokasi_kerja" name="lokasi_kerja" value="{{ old('lokasi_kerja', $employee->lokasi_kerja) }}" 
                                   placeholder="Contoh: Kandang A, Srengat">
                            @error('lokasi_kerja')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    @else
                    <!-- Hidden field for admin users - preserve current role -->
                    <input type="hidden" name="role" value="{{ $employee->role }}">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="kandang_id" class="form-label">Kandang</label>
                            <select class="form-select @error('kandang_id') is-invalid @enderror" id="kandang_id" name="kandang_id">
                                <option value="">Pilih Kandang</option>
                                @foreach(\App\Models\Kandang::with('lokasi')->get() as $kandang)
                                    <option value="{{ $kandang->id }}" {{ old('kandang_id', $employee->kandang_id) == $kandang->id ? 'selected' : '' }}>
                                        {{ $kandang->nama_kandang }} 
                                        @if($kandang->lokasi)
                                            ({{ $kandang->lokasi->nama_lokasi }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('kandang_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lokasi_kerja" class="form-label">Lokasi Kerja</label>
                            <input type="text" class="form-control @error('lokasi_kerja') is-invalid @enderror" 
                                   id="lokasi_kerja" name="lokasi_kerja" value="{{ old('lokasi_kerja', $employee->lokasi_kerja) }}" 
                                   placeholder="Contoh: Kandang A, Srengat">
                            @error('lokasi_kerja')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    @endif

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
