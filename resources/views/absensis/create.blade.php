@extends('layouts.app')

@section('title', 'Tambah Absensi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-plus-circle"></i> Tambah Absensi</h1>
    <a href="{{ route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h5 class="card-title mb-0">Form Tambah Absensi</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route(auth()->user()->isManager() ? 'manager.absensis.store' : 'admin.absensis.store') }}">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="karyawan_tipe" class="form-label">Tipe Karyawan <span class="text-danger">*</span></label>
                    <select class="form-control @error('karyawan_tipe') is-invalid @enderror"
                            id="karyawan_tipe" name="karyawan_tipe" required>
                        <option value="">Pilih Tipe Karyawan</option>
                        <option value="gudang" {{ old('karyawan_tipe') == 'gudang' ? 'selected' : '' }}>
                            Gudang
                        </option>
                        <option value="mandor" {{ old('karyawan_tipe') == 'mandor' ? 'selected' : '' }}>
                            Mandor
                        </option>
                    </select>
                    @error('karyawan_tipe')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="karyawan_id" class="form-label">Karyawan <span class="text-danger">*</span></label>
                    <select class="form-control @error('karyawan_id') is-invalid @enderror"
                            id="karyawan_id" name="karyawan_id" required>
                        <option value="">Pilih Karyawan</option>
                        @foreach(\App\Models\Gudang::all() as $gudang)
                            <option value="gudang_{{ $gudang->id }}" {{ old('karyawan_id') == 'gudang_' . $gudang->id ? 'selected' : '' }}>
                                [Gudang] {{ $gudang->nama }}
                            </option>
                        @endforeach
                        @foreach(\App\Models\Mandor::all() as $mandor)
                            <option value="mandor_{{ $mandor->id }}" {{ old('karyawan_id') == 'mandor_' . $mandor->id ? 'selected' : '' }}>
                                [Mandor] {{ $mandor->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('karyawan_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('tanggal') is-invalid @enderror"
                           id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                    @error('tanggal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-control @error('status') is-invalid @enderror"
                            id="status" name="status" required>
                        <option value="">Pilih Status</option>
                        <option value="full" {{ old('status') == 'full' ? 'selected' : '' }}>
                            Full Day
                        </option>
                        <option value="setengah_hari" {{ old('status') == 'setengah_hari' ? 'selected' : '' }}>
                            Setengah Hari
                        </option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index') }}"
                   class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Filter karyawan based on tipe selection
    $('#karyawan_tipe').change(function() {
        var tipe = $(this).val();
        var karyawanSelect = $('#karyawan_id');
        var options = karyawanSelect.find('option');
        
        // Hide all options first
        options.hide();
        options.first().show(); // Show "Pilih Karyawan"
        
        if (tipe === 'gudang') {
            options.filter('[value^="gudang_"]').show();
        } else if (tipe === 'mandor') {
            options.filter('[value^="mandor_"]').show();
        } else {
            options.show(); // Show all if no tipe selected
        }
        
        // Reset selection
        karyawanSelect.val('');
    });
});
</script>
@endpush