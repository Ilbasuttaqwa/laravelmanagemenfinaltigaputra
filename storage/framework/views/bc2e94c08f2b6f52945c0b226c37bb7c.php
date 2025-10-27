<?php $__env->startSection('title', 'Laporan Gaji'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">
                    <i class="bi bi-cash-stack"></i>
                    Laporan Gaji
                </h1>
                <p class="page-subtitle">Sistem laporan gaji terintegrasi dengan semua fitur</p>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end align-items-center mb-4">
                <div>
                    <a href="<?php echo e(route(auth()->user()->isAdmin() ? 'admin.salary-reports.export' : 'manager.salary-reports.export', array_merge(request()->query(), ['report_type' => 'biaya_gaji']))); ?>" 
                       class="btn btn-success" target="_blank">
                        <i class="bi bi-file-earmark-excel"></i>
                        Export Biaya Gaji
                    </a>
                    <a href="<?php echo e(route(auth()->user()->isAdmin() ? 'admin.salary-reports.export' : 'manager.salary-reports.export', array_merge(request()->query(), ['report_type' => 'rinci']))); ?>" 
                       class="btn btn-info" target="_blank">
                        <i class="bi bi-file-earmark-text"></i>
                        Export Rinci
                    </a>
                    <a href="<?php echo e(route(auth()->user()->isAdmin() ? 'admin.salary-reports.export' : 'manager.salary-reports.export', array_merge(request()->query(), ['report_type' => 'singkat']))); ?>" 
                       class="btn btn-warning" target="_blank">
                        <i class="bi bi-file-earmark"></i>
                        Export Singkat
                    </a>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route(auth()->user()->isAdmin() ? 'admin.salary-reports.index' : 'manager.salary-reports.index')); ?>" class="row g-3 filter-form">
                        <div class="col-md-2">
                            <label for="tahun" class="form-label">Tahun</label>
                            <input type="number" 
                                   name="tahun" 
                                   id="tahun" 
                                   class="form-control" 
                                   value="<?php echo e($tahun); ?>" 
                                   min="2020" 
                                   max="2030"
                                   placeholder="Tahun">
                        </div>
                        <div class="col-md-2">
                            <label for="bulan" class="form-label">Bulan</label>
                            <select name="bulan" id="bulan" class="form-select">
                                <?php $__currentLoopData = $availableMonths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $monthNum => $monthName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($monthNum); ?>" <?php echo e($bulan == $monthNum ? 'selected' : ''); ?>><?php echo e($monthName); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="tipe" class="form-label">Tipe Karyawan</label>
                            <select name="tipe" id="tipe" class="form-select">
                                <option value="all" <?php echo e($tipe == 'all' ? 'selected' : ''); ?>>Semua</option>
                                <option value="gudang" <?php echo e($tipe == 'gudang' ? 'selected' : ''); ?>>Gudang</option>
                                <?php if(auth()->user()->isManager()): ?>
                                    <option value="mandor" <?php echo e($tipe == 'mandor' ? 'selected' : ''); ?>>Mandor</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="lokasi_id" class="form-label">Lokasi</label>
                            <select name="lokasi_id" id="lokasi_id" class="form-select">
                                <option value="">Semua Lokasi</option>
                                <?php $__currentLoopData = $lokasis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lokasi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($lokasi->id); ?>" <?php echo e($lokasiId == $lokasi->id ? 'selected' : ''); ?>>
                                        <?php echo e($lokasi->nama_lokasi); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="kandang_id" class="form-label">Kandang</label>
                            <select name="kandang_id" id="kandang_id" class="form-select">
                                <option value="">Semua Kandang</option>
                                <?php $__currentLoopData = $kandangs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kandang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($kandang->id); ?>" <?php echo e($kandangId == $kandang->id ? 'selected' : ''); ?>>
                                        <?php echo e($kandang->nama_kandang); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="pembibitan_id" class="form-label">Pembibitan</label>
                            <select name="pembibitan_id" id="pembibitan_id" class="form-select">
                                <option value="">Semua Pembibitan</option>
                                <?php $__currentLoopData = $pembibitans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pembibitan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($pembibitan->id); ?>" <?php echo e($pembibitanId == $pembibitan->id ? 'selected' : ''); ?>>
                                        <?php echo e($pembibitan->judul); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" 
                                   value="<?php echo e($tanggalMulai); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" 
                                   value="<?php echo e($tanggalSelesai); ?>">
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i>
                                Filter
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                                <i class="bi bi-arrow-clockwise"></i>
                                Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Reports Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-table"></i>
                        Daftar Laporan Gaji
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($reports->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Karyawan</th>
                                        <th>Tipe</th>
                                        <th>Lokasi</th>
                                        <th>Kandang</th>
                                        <th>Pembibitan</th>
                                        <th>Jml Hari Kerja</th>
                                        <th>Gaji Saat Ini</th>
                                        <th>Gaji Pokok</th>
                                        <th>Total Gaji</th>
                                        <th>Periode</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td class="text-center"><?php echo e($index + 1); ?></td>
                                            <td>
                                                <strong><?php echo e($report->nama_karyawan); ?></strong>
                                            </td>
                                            <td class="text-center">
                                                <?php echo $report->tipe_karyawan_badge; ?>

                                            </td>
                                            <td><?php echo e($report->lokasi->nama_lokasi ?? '-'); ?></td>
                                            <td><?php echo e($report->kandang->nama_kandang ?? '-'); ?></td>
                                            <td><?php echo e($report->pembibitan->judul ?? '-'); ?></td>
                                            <td class="text-center"><?php echo e($report->jml_hari_kerja); ?></td>
                                            <td class="text-end"><?php echo e($report->gaji_pokok_formatted); ?></td>
                                            <td class="text-end"><?php echo e($report->gaji_pokok_asli_formatted); ?></td>
                                            <td class="text-end"><?php echo e($report->total_gaji_formatted); ?></td>
                                            <td class="text-center"><?php echo e($report->periode); ?></td>
                                            <td class="text-center">
                                                <a href="<?php echo e(route(auth()->user()->isAdmin() ? 'admin.salary-reports.show' : 'manager.salary-reports.show', $report)); ?>" 
                                                   class="btn btn-sm btn-info" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="7" class="text-end">Total:</th>
                                        <th class="text-end"><?php echo e('Rp ' . number_format($reports->sum('gaji_pokok'), 0, ',', '.')); ?></th>
                                        <th class="text-end">-</th>
                                        <th class="text-end"><?php echo e('Rp ' . number_format($reports->sum('total_gaji'), 0, ',', '.')); ?></th>
                                        <th colspan="2"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle"></i>
                            Tidak ada data laporan gaji untuk periode yang dipilih.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* Perbaikan tampilan tabel - kolom lebih proporsional */
.table {
    table-layout: fixed;
    width: 100%;
}

.table th,
.table td {
    padding: 8px 4px;
    font-size: 13px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Kolom NO - kecil */
.table th:nth-child(1),
.table td:nth-child(1) {
    width: 3%;
    text-align: center;
}

/* Kolom Nama Karyawan - sedang */
.table th:nth-child(2),
.table td:nth-child(2) {
    width: 13%;
}

/* Kolom Tipe - kecil */
.table th:nth-child(3),
.table td:nth-child(3) {
    width: 8%;
    text-align: center;
}

/* Kolom Lokasi - kecil */
.table th:nth-child(4),
.table td:nth-child(4) {
    width: 6%;
    text-align: center;
}

/* Kolom Kandang - kecil */
.table th:nth-child(5),
.table td:nth-child(5) {
    width: 6%;
    text-align: center;
}

/* Kolom Pembibitan - kecil */
.table th:nth-child(6),
.table td:nth-child(6) {
    width: 7%;
    text-align: center;
}

/* Kolom Jml Hari Kerja - sedang */
.table th:nth-child(7),
.table td:nth-child(7) {
    width: 10%;
    text-align: center;
}

/* Kolom Gaji Saat Ini - sedang */
.table th:nth-child(8),
.table td:nth-child(8) {
    width: 9%;
    text-align: right;
}

/* Kolom Gaji Pokok - sedang */
.table th:nth-child(9),
.table td:nth-child(9) {
    width: 9%;
    text-align: right;
}

/* Kolom Total Gaji - sedang */
.table th:nth-child(10),
.table td:nth-child(10) {
    width: 9%;
    text-align: right;
}

/* Kolom Periode - kecil */
.table th:nth-child(11),
.table td:nth-child(11) {
    width: 7%;
    text-align: center;
}

/* Kolom Aksi - kecil */
.table th:nth-child(12),
.table td:nth-child(12) {
    width: 5%;
    text-align: center;
}

/* Filter form lebih kecil */
.filter-form .form-control,
.filter-form .form-select {
    font-size: 12px;
    padding: 4px 8px;
    height: auto;
}

.filter-form .form-label {
    font-size: 11px;
    margin-bottom: 2px;
    font-weight: 500;
}

.filter-form .btn {
    font-size: 12px;
    padding: 4px 12px;
}

/* Badge lebih kecil */
.badge {
    font-size: 10px;
    padding: 2px 6px;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Production ready - no realtime requirements
document.addEventListener('DOMContentLoaded', function() {
    console.log('Salary Reports loaded successfully');
});

// Reset filters function
function resetFilters() {
    // Reset all form fields to empty values
    document.getElementById('tahun').value = '';
    document.getElementById('bulan').value = '';
    document.getElementById('tipe').value = '';
    document.getElementById('lokasi_id').value = '';
    document.getElementById('kandang_id').value = '';
    document.getElementById('pembibitan_id').value = '';
    document.getElementById('tanggal_mulai').value = '';
    document.getElementById('tanggal_selesai').value = '';
    
    // Clear the table content
    const tableBody = document.querySelector('table tbody');
    if (tableBody) {
        tableBody.innerHTML = '<tr><td colspan="11" class="text-center text-muted">Tidak ada data yang ditemukan</td></tr>';
    }
    
    // Clear totals
    const totalsRow = document.querySelector('table tfoot tr');
    if (totalsRow) {
        totalsRow.innerHTML = '<td colspan="11" class="text-center text-muted">-</td>';
    }
    
    // Hide export buttons
    const exportButtons = document.querySelectorAll('.export-buttons a');
    exportButtons.forEach(btn => {
        btn.style.display = 'none';
    });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Managemen\resources\views/salary-reports/index.blade.php ENDPATH**/ ?>