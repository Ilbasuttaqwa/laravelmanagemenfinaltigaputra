@extends('layouts.app')

@section('title', 'Detail Laporan Gaji')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">
                    <i class="bi bi-cash-stack"></i>
                    Detail Laporan Gaji
                </h1>
                <p class="page-subtitle">Detail lengkap laporan gaji karyawan</p>
            </div>

            <!-- Back Button -->
            <div class="mb-4">
                <a href="{{ route('manager.salary-reports.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i>
                    Kembali ke Daftar Laporan
                </a>
            </div>

            <!-- Report Details -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-person"></i>
                                Informasi Karyawan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Nama Karyawan:</strong></td>
                                            <td>{{ $salaryReport->nama_karyawan }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tipe Karyawan:</strong></td>
                                            <td>{!! $salaryReport->tipe_karyawan_badge !!}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Lokasi:</strong></td>
                                            <td>{{ $salaryReport->lokasi->nama_lokasi ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Kandang:</strong></td>
                                            <td>{{ $salaryReport->kandang->nama_kandang ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Pembibitan:</strong></td>
                                            <td>{{ $salaryReport->pembibitan->judul ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Periode:</strong></td>
                                            <td>{{ $salaryReport->periode }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Range:</strong></td>
                                            <td>{{ $salaryReport->tanggal_range }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Jumlah Hari Kerja:</strong></td>
                                            <td>{{ $salaryReport->jml_hari_kerja }} hari</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-calculator"></i>
                                Rincian Gaji
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Gaji Pokok:</span>
                                <strong class="text-primary">{{ $salaryReport->gaji_pokok_formatted }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Jumlah Hari Kerja:</span>
                                <strong>{{ $salaryReport->jml_hari_kerja }} hari</strong>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5">Total Gaji:</span>
                                <strong class="h5 text-success">{{ $salaryReport->total_gaji_formatted }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Data -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-link-45deg"></i>
                                Data Terkait
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($salaryReport->lokasi)
                                <div class="col-md-4">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0">Lokasi</h6>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Nama:</strong> {{ $salaryReport->lokasi->nama_lokasi }}</p>
                                            <p><strong>Deskripsi:</strong> {{ $salaryReport->lokasi->deskripsi }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($salaryReport->kandang)
                                <div class="col-md-4">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0">Kandang</h6>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Nama:</strong> {{ $salaryReport->kandang->nama_kandang }}</p>
                                            <p><strong>Deskripsi:</strong> {{ $salaryReport->kandang->deskripsi }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($salaryReport->pembibitan)
                                <div class="col-md-4">
                                    <div class="card border-info">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0">Pembibitan</h6>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Judul:</strong> {{ $salaryReport->pembibitan->judul }}</p>
                                            <p><strong>Tanggal Mulai:</strong> {{ $salaryReport->pembibitan->tanggal_mulai->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
