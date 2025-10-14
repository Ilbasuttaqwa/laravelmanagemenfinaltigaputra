@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i> Laporan Absensi Bulanan
                    </h3>
                    <div class="real-time-info">
                        <i class="fas fa-clock me-1"></i>
                        <span id="current-time">Loading...</span>
                        <small class="ms-2">(Waktu Jakarta)</small>
                    </div>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#generateModal">
                            <i class="fas fa-plus"></i> Generate Laporan
                        </button>
                        <a href="{{ route('manager.monthly-attendance-reports.export', request()->query()) }}" class="btn btn-success">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('manager.monthly-attendance-reports.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="tahun">Tahun:</label>
                                <input type="number" 
                                       name="tahun" 
                                       id="tahun" 
                                       class="form-control" 
                                       value="{{ $tahun }}" 
                                       min="2020" 
                                       max="2030"
                                       placeholder="Masukkan tahun">
                                <small class="text-muted">Range: 2020-2030</small>
                            </div>
                            <div class="col-md-3">
                                <label for="bulan">Bulan:</label>
                                <select name="bulan" id="bulan" class="form-control">
                                    @foreach($availableMonths as $monthNum => $monthName)
                                        <option value="{{ $monthNum }}" {{ $bulan == $monthNum ? 'selected' : '' }}>
                                            {{ $monthName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="tipe">Tipe Karyawan:</label>
                                <select name="tipe" id="tipe" class="form-control">
                                    <option value="all" {{ $tipe == 'all' ? 'selected' : '' }}>Semua</option>
                                    <option value="gudang" {{ $tipe == 'gudang' ? 'selected' : '' }}>Gudang</option>
                                    <option value="mandor" {{ $tipe == 'mandor' ? 'selected' : '' }}>Mandor</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-info btn-block">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Reports Table -->
                    @if($reports->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Karyawan</th>
                                        <th>Tipe</th>
                                        <th>Periode</th>
                                        <th>Total Hari Kerja</th>
                                        <th>Full Day</th>
                                        <th>Half Day</th>
                                        <th>Absen</th>
                                        <th>Persentase Kehadiran</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports as $index => $report)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $report->nama_karyawan }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $report->tipe_karyawan == 'gudang' ? 'primary' : 'success' }}">
                                                    {{ ucfirst($report->tipe_karyawan) }}
                                                </span>
                                            </td>
                                            <td>{{ $report->periode }}</td>
                                            <td>{{ $report->total_hari_kerja }}</td>
                                            <td>
                                                <span class="badge badge-success">{{ $report->total_hari_full }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-warning">{{ $report->total_hari_setengah }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-danger">{{ $report->total_hari_absen }}</span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
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
                                                        {{ $report->persentase_kehadiran_formatted }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('manager.monthly-attendance-reports.show', $report) }}" 
                                                   class="btn btn-sm btn-info" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i>
                            Tidak ada data laporan untuk periode yang dipilih.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Generate Modal -->
<div class="modal fade" id="generateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('manager.monthly-attendance-reports.generate') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Generate Laporan Bulanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="generate_tahun">Tahun:</label>
                        <input type="number" 
                               name="tahun" 
                               id="generate_tahun" 
                               class="form-control" 
                               value="{{ date('Y') }}" 
                               min="2020" 
                               max="2030"
                               placeholder="Masukkan tahun"
                               required>
                        <small class="text-muted">Range: 2020-2030</small>
                    </div>
                    <div class="form-group">
                        <label for="generate_bulan">Bulan:</label>
                        <select name="bulan" id="generate_bulan" class="form-control" required>
                            @foreach($availableMonths as $monthNum => $monthName)
                                <option value="{{ $monthNum }}" {{ $monthNum == date('n') ? 'selected' : '' }}>
                                    {{ $monthName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-cog"></i> Generate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.real-time-info {
    background: rgba(0, 123, 255, 0.1);
    padding: 8px 16px;
    border-radius: 20px;
    display: inline-block;
    margin-top: 10px;
    font-size: 14px;
    font-weight: 500;
    border: 1px solid rgba(0, 123, 255, 0.2);
}

.real-time-info i {
    color: #007bff;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Real-time clock for Jakarta timezone
    function updateJakartaTime() {
        const now = new Date();
        const jakartaTime = new Date(now.toLocaleString("en-US", {timeZone: "Asia/Jakarta"}));
        
        const options = {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            timeZone: 'Asia/Jakarta'
        };
        
        const timeString = jakartaTime.toLocaleDateString('id-ID', options);
        $('#current-time').text(timeString);
    }
    
    // Update time every second
    updateJakartaTime();
    setInterval(updateJakartaTime, 1000);
});
</script>
@endpush

@push('styles')
<style>
/* Modern Design System */
:root {
    --primary-color: #2563eb;
    --secondary-color: #64748b;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --light-bg: #ffffff;
    --border-color: #d1d5db;
    --text-primary: #111827;
    --text-secondary: #374151;
    --text-muted: #6b7280;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
    --radius-sm: 0.375rem;
    --radius-md: 0.5rem;
    --radius-lg: 0.75rem;
}

/* Force Text Visibility */
body {
    color: var(--text-primary) !important;
}

.text-dark {
    color: var(--text-primary) !important;
}

.text-muted {
    color: var(--text-muted) !important;
}

.text-primary {
    color: var(--primary-color) !important;
}

.text-success {
    color: var(--success-color) !important;
}

.text-warning {
    color: var(--warning-color) !important;
}

/* Table Styling */
.table {
    border-collapse: separate;
    border-spacing: 0;
    background: white;
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-md);
}

.table th {
    border: none;
    font-weight: 700;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    background: linear-gradient(135deg, #1e293b, #334155);
    color: #ffffff !important;
    padding: 1rem 0.75rem;
    text-align: center;
}

.table td {
    vertical-align: middle;
    padding: 1rem 0.75rem;
    border: none;
    border-bottom: 1px solid var(--border-color);
    background: #ffffff;
    color: var(--text-primary) !important;
}

.table tbody tr:hover {
    background: #f8fafc !important;
}

.table tbody tr:hover td {
    color: var(--text-primary) !important;
}

/* Enhanced Badges */
.badge {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: var(--radius-md);
    text-transform: uppercase;
    letter-spacing: 0.025em;
    color: #ffffff !important;
}

.badge-primary {
    background: linear-gradient(135deg, var(--primary-color), #3b82f6);
    color: #ffffff !important;
}

.badge-success {
    background: linear-gradient(135deg, var(--success-color), #34d399);
    color: #ffffff !important;
}

.badge-warning {
    background: linear-gradient(135deg, var(--warning-color), #fbbf24);
    color: #ffffff !important;
}

.badge-danger {
    background: linear-gradient(135deg, var(--danger-color), #f87171);
    color: #ffffff !important;
}

/* Progress Bar */
.progress {
    background-color: #e9ecef;
    border-radius: var(--radius-md);
    height: 20px;
}

.progress-bar {
    color: #ffffff !important;
    font-weight: 600;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.progress-bar.bg-success {
    background: linear-gradient(135deg, var(--success-color), #34d399) !important;
}

.progress-bar.bg-warning {
    background: linear-gradient(135deg, var(--warning-color), #fbbf24) !important;
}

.progress-bar.bg-danger {
    background: linear-gradient(135deg, var(--danger-color), #f87171) !important;
}

/* Form Controls */
.form-control {
    border: 2px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    color: var(--text-primary) !important;
    background: #ffffff;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgb(37 99 235 / 0.1);
    color: var(--text-primary) !important;
}

label {
    color: var(--text-primary) !important;
    font-weight: 600;
}

/* Card Styling */
.card {
    border: none;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    background: white;
}

.card-header {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border: none;
    font-weight: 700;
    color: var(--text-primary);
}

.card-title {
    color: var(--text-primary) !important;
    font-weight: 700;
}

/* Alert Styling */
.alert {
    color: var(--text-primary) !important;
}

.alert-info {
    background-color: #dbeafe !important;
    border-color: #93c5fd !important;
    color: #1e40af !important;
}

/* Button Styling */
.btn {
    border-radius: var(--radius-md);
    font-weight: 500;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color), #3b82f6);
    border: none;
    color: #ffffff !important;
}

.btn-success {
    background: linear-gradient(135deg, var(--success-color), #34d399);
    border: none;
    color: #ffffff !important;
}

.btn-info {
    background: linear-gradient(135deg, #0ea5e9, #38bdf8);
    border: none;
    color: #ffffff !important;
}

.btn-secondary {
    background: linear-gradient(135deg, var(--secondary-color), #94a3b8);
    border: none;
    color: #ffffff !important;
}
</style>
@endpush
