@extends('layouts.app')

@section('title', 'Manage - Tiga Putra Management System')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-gear"></i>
        Manage Karyawan
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            Tambah Karyawan
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-people"></i>
            Daftar Karyawan
        </h5>
    </div>
    <div class="card-body">
        @if($employees->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Nama Karyawan</th>
                            <th width="15%">Gaji</th>
                            <th width="20%">Tanggal Dibuat</th>
                            <th width="20%">Tanggal Diperbarui</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $index => $employee)
                            <tr>
                                <td>{{ $employees->firstItem() + $index }}</td>
                                <td>
                                    <strong>{{ $employee->nama }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        Rp {{ number_format($employee->gaji, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $employee->created_at->format('d/m/Y H:i') }}
                                    </small>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $employee->updated_at->format('d/m/Y H:i') }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.employees.show', $employee) }}" 
                                           class="btn btn-outline-info btn-sm" 
                                           title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.employees.edit', $employee) }}" 
                                           class="btn btn-outline-warning btn-sm" 
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="pagination-info">
                    Menampilkan {{ $employees->firstItem() }} sampai {{ $employees->lastItem() }} 
                    dari {{ $employees->total() }} data
                </div>
                <div>
                    {{ $employees->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-people display-1 text-muted"></i>
                <h4 class="text-muted mt-3">Belum Ada Data Karyawan</h4>
                <p class="text-muted">Silakan tambah karyawan terlebih dahulu.</p>
                <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i>
                    Tambah Karyawan Pertama
                </a>
            </div>
        @endif
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-info-circle"></i>
                    Informasi
                </h6>
            </div>
            <div class="card-body">
                <p class="card-text">
                    <strong>Admin dapat:</strong>
                </p>
                <ul class="mb-0">
                    <li>Melihat daftar semua karyawan</li>
                    <li>Menambah karyawan baru</li>
                    <li>Mengedit data karyawan</li>
                    <li>Melihat detail karyawan</li>
                </ul>
                <hr>
                <p class="card-text">
                    <strong>Admin tidak dapat:</strong>
                </p>
                <ul class="mb-0">
                    <li>Membuat karyawan dengan role mandor</li>
                    <li>Menghapus data karyawan</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-graph-up"></i>
                    Statistik
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-primary">{{ $employees->total() }}</h4>
                        <small class="text-muted">Total Karyawan</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">
                            Rp {{ number_format($employees->sum('gaji'), 0, ',', '.') }}
                        </h4>
                        <small class="text-muted">Total Gaji</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
