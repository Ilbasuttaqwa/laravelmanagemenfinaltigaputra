@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user"></i> Detail Laporan Absensi - {{ $report->nama_karyawan }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('manager.monthly-attendance-reports.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Employee Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-user"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Nama Karyawan</span>
                                    <span class="info-box-number">{{ $report->nama_karyawan }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary">
                                    <i class="fas fa-briefcase"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Tipe Karyawan</span>
                                    <span class="info-box-number">{{ ucfirst($report->tipe_karyawan) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-calendar-check"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Hari Kerja</span>
                                    <span class="info-box-number">{{ $report->total_hari_kerja }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Full Day</span>
                                    <span class="info-box-number">{{ $report->total_hari_full }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning">
                                    <i class="fas fa-clock"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Half Day</span>
                                    <span class="info-box-number">{{ $report->total_hari_setengah }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger">
                                    <i class="fas fa-times-circle"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Absen</span>
                                    <span class="info-box-number">{{ $report->total_hari_absen }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Percentage -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Persentase Kehadiran</h3>
                                </div>
                                <div class="card-body">
                                    <div class="progress" style="height: 30px;">
                                        <div class="progress-bar 
                                            @if($report->persentase_kehadiran >= 90) bg-success
                                            @elseif($report->persentase_kehadiran >= 70) bg-warning
                                            @else bg-danger
                                            @endif" 
                                            role="progressbar" 
                                            style="width: {{ $report->persentase_kehadiran }}%"
                                            aria-valuenow="{{ $report->persentase_kehadiran }}" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100">
                                            <strong>{{ $report->persentase_kehadiran_formatted }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Daily Attendance -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Detail Absensi Harian - {{ $report->periode }}</h3>
                                </div>
                                <div class="card-body">
                                    @if($absensis->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Tanggal</th>
                                                        <th>Hari</th>
                                                        <th>Status</th>
                                                        <th>Keterangan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($absensis as $index => $absensi)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $absensi->tanggal->format('d/m/Y') }}</td>
                                                            <td>{{ $absensi->tanggal->format('l') }}</td>
                                                            <td>
                                                                <span class="badge {{ $absensi->status_badge }}">
                                                                    {{ $absensi->status_label }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $absensi->keterangan ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info text-center">
                                            <i class="fas fa-info-circle"></i>
                                            Tidak ada data absensi untuk periode ini.
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
</div>
@endsection
