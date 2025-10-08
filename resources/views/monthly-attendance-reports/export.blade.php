@php
    $bulanNames = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $namaBulan = $bulanNames[$bulan] ?? 'Bulan Tidak Valid';
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi Bulanan - {{ $namaBulan }} {{ $tahun }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 24px;
        }
        
        .header h2 {
            color: #7f8c8d;
            margin: 10px 0 0 0;
            font-size: 18px;
            font-weight: normal;
        }
        
        .info-section {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        
        .info-label {
            font-weight: bold;
            color: #495057;
        }
        
        .info-value {
            color: #2c3e50;
        }
        
        .table-container {
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        
        th {
            background-color: #2c3e50;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tr:hover {
            background-color: #e3f2fd;
        }
        
        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .badge-gudang {
            background-color: #007bff;
            color: white;
        }
        
        .badge-mandor {
            background-color: #28a745;
            color: white;
        }
        
        .progress-bar {
            width: 100%;
            height: 20px;
            background-color: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }
        
        .progress-fill {
            height: 100%;
            background-color: #28a745;
            transition: width 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 10px;
            font-weight: bold;
        }
        
        .progress-fill.low {
            background-color: #dc3545;
        }
        
        .progress-fill.medium {
            background-color: #ffc107;
            color: #212529;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #6c757d;
            font-size: 12px;
        }
        
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            
            .header, .info-section, .table-container {
                box-shadow: none;
                border: 1px solid #ddd;
            }
            
            .footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Absensi Bulanan</h1>
        <h2>{{ $namaBulan }} {{ $tahun }}</h2>
        <p>Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
    </div>

    <div class="info-section">
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Periode:</span>
                <span class="info-value">{{ $namaBulan }} {{ $tahun }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Tipe Karyawan:</span>
                <span class="info-value">{{ $tipe === 'all' ? 'Semua' : ucfirst($tipe) }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Total Karyawan:</span>
                <span class="info-value">{{ $reports->count() }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Total Hari Kerja:</span>
                <span class="info-value">{{ $reports->first()->total_hari_kerja ?? 0 }} hari</span>
            </div>
        </div>

        <div class="summary-stats">
            <div class="stat-card">
                <div class="stat-number">{{ $reports->sum('full_day_count') }}</div>
                <div class="stat-label">Total Full Day</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $reports->sum('half_day_count') }}</div>
                <div class="stat-label">Total Half Day</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $reports->sum('absen_count') }}</div>
                <div class="stat-label">Total Absen</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ number_format($reports->avg('persentase_kehadiran'), 1) }}%</div>
                <div class="stat-label">Rata-rata Kehadiran</div>
            </div>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="25%">Nama Karyawan</th>
                    <th width="10%">Tipe</th>
                    <th width="15%">Total Hari Kerja</th>
                    <th width="12%">Full Day</th>
                    <th width="12%">Half Day</th>
                    <th width="12%">Absen</th>
                    <th width="15%">Persentase Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $index => $report)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $report->nama_karyawan }}</strong>
                        </td>
                        <td>
                            <span class="badge badge-{{ $report->tipe_karyawan }}">
                                {{ ucfirst($report->tipe_karyawan) }}
                            </span>
                        </td>
                        <td>{{ $report->total_hari_kerja }}</td>
                        <td>{{ $report->full_day_count }}</td>
                        <td>{{ $report->half_day_count }}</td>
                        <td>{{ $report->absen_count }}</td>
                        <td>
                            <div class="progress-bar">
                                <div class="progress-fill {{ $report->persentase_kehadiran < 50 ? 'low' : ($report->persentase_kehadiran < 80 ? 'medium' : '') }}" 
                                     style="width: {{ $report->persentase_kehadiran }}%">
                                    {{ number_format($report->persentase_kehadiran, 1) }}%
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: #6c757d;">
                            <strong>Tidak ada data absensi untuk periode ini</strong>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh Sistem Manajemen Tiga Putra</p>
        <p>Untuk pertanyaan lebih lanjut, hubungi administrator sistem</p>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
