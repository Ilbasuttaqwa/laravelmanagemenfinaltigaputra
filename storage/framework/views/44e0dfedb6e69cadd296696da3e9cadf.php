<?php $__env->startSection('title', 'Manage - Tiga Putra Management System'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-gear"></i>
        Manage Karyawan
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?php echo e(route('admin.employees.create')); ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            Tambah Karyawan
        </a>
    </div>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i>
        <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle"></i>
        <?php echo e(session('error')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-people"></i>
            Daftar Karyawan
        </h5>
    </div>
    <div class="card-body">
        <?php if($employees->count() > 0): ?>
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
                        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($employees->firstItem() + $index); ?></td>
                                <td>
                                    <strong><?php echo e($employee->nama); ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        Rp <?php echo e(number_format($employee->gaji, 0, ',', '.')); ?>

                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?php echo e($employee->created_at->format('d/m/Y H:i')); ?>

                                    </small>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?php echo e($employee->updated_at->format('d/m/Y H:i')); ?>

                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?php echo e(route('admin.employees.show', $employee)); ?>" 
                                           class="btn btn-outline-info btn-sm" 
                                           title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('admin.employees.edit', $employee)); ?>" 
                                           class="btn btn-outline-warning btn-sm" 
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="pagination-info">
                    Menampilkan <?php echo e($employees->firstItem()); ?> sampai <?php echo e($employees->lastItem()); ?> 
                    dari <?php echo e($employees->total()); ?> data
                </div>
                <div>
                    <?php echo e($employees->links()); ?>

                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-people display-1 text-muted"></i>
                <h4 class="text-muted mt-3">Belum Ada Data Karyawan</h4>
                <p class="text-muted">Silakan tambah karyawan terlebih dahulu.</p>
                <a href="<?php echo e(route('admin.employees.create')); ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i>
                    Tambah Karyawan Pertama
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-graph-up"></i>
                    Statistik
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-6">
                        <h4 class="text-primary"><?php echo e($employees->total()); ?></h4>
                        <small class="text-muted">Total Karyawan</small>
                    </div>
                    <div class="col-md-6">
                        <h4 class="text-success">
                            Rp <?php echo e(number_format($employees->sum('gaji'), 0, ',', '.')); ?>

                        </h4>
                        <small class="text-muted">Total Gaji</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Managemen\resources\views/admin/manage.blade.php ENDPATH**/ ?>