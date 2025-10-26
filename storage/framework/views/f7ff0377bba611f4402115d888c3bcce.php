<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Gaji - <?php echo e($availableMonths[$bulan]); ?> <?php echo e($tahun); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        
        .header h2 {
            margin: 10px 0 0 0;
            font-size: 18px;
            color: #666;
        }
        
        .report-info {
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        
        .report-info table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .report-info td {
            padding: 5px 10px;
            border: none;
        }
        
        .report-info td:first-child {
            font-weight: bold;
            width: 150px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .table th,
        .table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        
        .table td {
            text-align: left;
        }
        
        .table td.number {
            text-align: right;
        }
        
        .table td.center {
            text-align: center;
        }
        
        .total-row {
            background-color: #e9ecef;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN GAJI</h1>
        <h2>
            <?php if($reportType == 'biaya_gaji'): ?>
                LAPORAN BIAYA GAJI RINCI
            <?php elseif($reportType == 'rinci'): ?>
                LAPORAN RINCI
            <?php else: ?>
                LAPORAN SINGKAT
            <?php endif; ?>
        </h2>
    </div>

    <div class="report-info">
        <table>
            <tr>
                <td>Periode:</td>
                <td><?php echo e($availableMonths[$bulan]); ?> <?php echo e($tahun); ?></td>
            </tr>
            <?php if($lokasiId): ?>
            <tr>
                <td>Lokasi:</td>
                <td><?php echo e(\App\Models\Lokasi::find($lokasiId)->nama_lokasi ?? '-'); ?></td>
            </tr>
            <?php endif; ?>
            <?php if($kandangId): ?>
            <tr>
                <td>Kandang:</td>
                <td><?php echo e(\App\Models\Kandang::find($kandangId)->nama_kandang ?? '-'); ?></td>
            </tr>
            <?php endif; ?>
            <?php if($pembibitanId): ?>
            <tr>
                <td>Pembibitan:</td>
                <td><?php echo e(\App\Models\Pembibitan::find($pembibitanId)->judul ?? '-'); ?></td>
            </tr>
            <?php endif; ?>
            <?php if($tanggalMulai && $tanggalSelesai): ?>
            <tr>
                <td>Tanggal:</td>
                <td><?php echo e(\Carbon\Carbon::parse($tanggalMulai)->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($tanggalSelesai)->format('d/m/Y')); ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td>Tipe Karyawan:</td>
                <td><?php echo e($tipe == 'all' ? 'Semua' : ucfirst($tipe)); ?></td>
            </tr>
            <tr>
                <td>Tanggal Cetak:</td>
                <td><?php echo e(\Carbon\Carbon::now()->format('d/m/Y H:i:s')); ?></td>
            </tr>
        </table>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Tipe</th>
                <th>Lokasi</th>
                <th>Kandang</th>
                <th>Pembibitan</th>
                <th>Jml Hari Kerja</th>
                <?php if($reportType == 'biaya_gaji'): ?>
                <th>Gaji Pokok</th>
                <th>Total Gaji</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="center"><?php echo e($index + 1); ?></td>
                <td><?php echo e($report->nama_karyawan); ?></td>
                <td class="center"><?php echo e(ucfirst($report->tipe_karyawan)); ?></td>
                <td><?php echo e($report->lokasi->nama_lokasi ?? '-'); ?></td>
                <td><?php echo e($report->kandang->nama_kandang ?? '-'); ?></td>
                <td><?php echo e($report->pembibitan->judul ?? '-'); ?></td>
                <td class="center"><?php echo e($report->jml_hari_kerja); ?></td>
                <?php if($reportType == 'biaya_gaji'): ?>
                <td class="number"><?php echo e($report->gaji_pokok_formatted); ?></td>
                <td class="number"><?php echo e($report->total_gaji_formatted); ?></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        <?php if($reportType == 'biaya_gaji'): ?>
        <tfoot>
            <tr class="total-row">
                <td colspan="7" class="center">TOTAL:</td>
                <td class="number"><?php echo e('Rp ' . number_format($reports->sum('gaji_pokok'), 0, ',', '.')); ?></td>
                <td class="number"><?php echo e('Rp ' . number_format($reports->sum('total_gaji'), 0, ',', '.')); ?></td>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>

    <div class="footer">
        <p>Dicetak pada <?php echo e(\Carbon\Carbon::now()->format('d/m/Y H:i:s')); ?> | Tiga Putra Management System</p>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
<?php /**PATH C:\laragon\www\Managemen\resources\views/salary-reports/export.blade.php ENDPATH**/ ?>