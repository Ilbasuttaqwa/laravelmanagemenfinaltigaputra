@extends('layouts.app')

@section('title', 'Edit Absensi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-pencil"></i> Edit Absensi</h1>
    <a href="{{ route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h5 class="card-title mb-0">Form Edit Absensi</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route(auth()->user()->isManager() ? 'manager.absensis.update' : 'admin.absensis.update', $absensi) }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="karyawan_tipe" class="form-label">Tipe Karyawan <span class="text-danger">*</span></label>
                    <select class="form-control @error('karyawan_tipe') is-invalid @enderror"
                            id="karyawan_tipe" name="karyawan_tipe" required>
                        <option value="">Pilih Tipe Karyawan</option>
                        <option value="gudang" {{ old('karyawan_tipe', $absensi->tipe_karyawan) == 'gudang' ? 'selected' : '' }}>
                            Gudang
                        </option>
                        <option value="mandor" {{ old('karyawan_tipe', $absensi->tipe_karyawan) == 'mandor' ? 'selected' : '' }}>
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
                        @if($absensi->tipe_karyawan == 'gudang')
                            @foreach($gudangs as $gudang)
                                <option value="{{ $gudang->id }}" {{ old('karyawan_id', $absensi->gudang_id) == $gudang->id ? 'selected' : '' }}>
                                    {{ $gudang->nama }}
                                </option>
                            @endforeach
                        @elseif($absensi->tipe_karyawan == 'mandor')
                            @foreach($mandors as $mandor)
                                <option value="{{ $mandor->id }}" {{ old('karyawan_id', $absensi->mandor_id) == $mandor->id ? 'selected' : '' }}>
                                    {{ $mandor->nama }}
                                </option>
                            @endforeach
                        @endif
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
                           id="tanggal" name="tanggal" value="{{ old('tanggal', $absensi->tanggal->format('Y-m-d')) }}" required>
                    @error('tanggal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-control @error('status') is-invalid @enderror"
                            id="status" name="status" required>
                        <option value="">Pilih Status</option>
                        <option value="full" {{ old('status', $absensi->status) == 'full' ? 'selected' : '' }}>
                            Full Day
                        </option>
                        <option value="setengah_hari" {{ old('status', $absensi->status) == 'setengah_hari' ? 'selected' : '' }}>
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
                    <i class="bi bi-save"></i> Update
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
    // Handle karyawan dropdown change
    $('#karyawan_tipe').change(function() {
        var tipe = $(this).val();
        var karyawanSelect = $('#karyawan_id');
        
        karyawanSelect.empty().append('<option value="">Pilih Karyawan</option>');
        
        if (tipe === 'gudang') {
            // Gudang options will be populated via AJAX
            $.get('/manager/absensis/get-employees', {tipe: 'gudang'}, function(data) {
                karyawanSelect.empty().append('<option value="">Pilih Karyawan</option>');
                $.each(data, function(index, employee) {
                    karyawanSelect.append('<option value="' + employee.id + '">' + employee.nama + '</option>');
                });
                // Set selected value after populating
                karyawanSelect.val('{{ $absensi->karyawan_id }}');
            });
        } else if (tipe === 'mandor') {
            // Mandor options will be populated via AJAX
            $.get('/manager/absensis/get-employees', {tipe: 'mandor'}, function(data) {
                karyawanSelect.empty().append('<option value="">Pilih Karyawan</option>');
                $.each(data, function(index, employee) {
                    karyawanSelect.append('<option value="' + employee.id + '">' + employee.nama + '</option>');
                });
                // Set selected value after populating
                karyawanSelect.val('{{ $absensi->karyawan_id }}');
            });
        }
    });
});
</script>
@endpush