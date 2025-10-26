<?php $__env->startSection('title', 'Master Karyawan'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-people"></i>
        Master Karyawan
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?php echo e(route(auth()->user()->isAdmin() ? 'admin.employees.create' : 'manager.employees.create')); ?>" 
           class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            Tambah Karyawan
        </a>
    </div>
</div>

<!-- Search & Filter Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route(auth()->user()->isAdmin() ? 'admin.employees.index' : 'manager.employees.index')); ?>">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Cari Karyawan</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?php echo e(request('search')); ?>" placeholder="Masukkan nama karyawan">
                </div>
                <div class="col-md-4">
                    <label for="kandang_id" class="form-label">Filter Kandang</label>
                    <select class="form-control" id="kandang_id" name="kandang_id">
                        <option value="">Semua Kandang</option>
                        <?php $__currentLoopData = $kandangs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kandang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($kandang->id); ?>" <?php echo e(request('kandang_id') == $kandang->id ? 'selected' : ''); ?>>
                                <?php echo e($kandang->nama_kandang); ?> 
                                <?php if($kandang->lokasi): ?>
                                    (<?php echo e($kandang->lokasi->nama_lokasi); ?>)
                                <?php endif; ?>
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                            Cari
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Employees Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Daftar Karyawan</h5>
    </div>
    <div class="card-body">
        <?php if($employees->count() > 0): ?>
            <div class="table-responsive" style="max-height: 500px; overflow-y: auto; overflow-x: auto;">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Role</th>
                            <th>Gaji</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($employees->firstItem() + $index); ?></td>
                                <td><?php echo e($employee->nama); ?></td>
                                <td>
                                    <?php if($employee->role === 'mandor'): ?>
                                        <span class="badge bg-warning">Mandor</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">Karyawan</span>
                                    <?php endif; ?>
                                </td>
                                <td>Rp <?php echo e(number_format($employee->gaji_pokok, 0, ',', '.')); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?php echo e(route(auth()->user()->isManager() ? 'manager.employees.show' : 'admin.employees.show', $employee)); ?>" 
                                           class="btn btn-info btn-sm" title="Lihat">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route(auth()->user()->isManager() ? 'manager.employees.edit' : 'admin.employees.edit', $employee)); ?>" 
                                           class="btn btn-warning btn-sm" title="Edit">
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
            <div class="d-flex justify-content-center">
                <?php echo e($employees->appends(request()->query())->links()); ?>

            </div>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                <h5 class="text-muted mt-3">Tidak ada data karyawan</h5>
                <p class="text-muted">Mulai dengan menambahkan karyawan baru.</p>
                <a href="<?php echo e(route(auth()->user()->isAdmin() ? 'admin.employees.create' : 'manager.employees.create')); ?>" 
                   class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i>
                    Tambah Karyawan Pertama
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mt-4">
    <div class="col-md-6 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total Karyawan</h6>
                        <h4 class="mb-0"><?php echo e(\App\Models\Employee::count()); ?></h4>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-people-fill fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<?php if(auth()->user()->isManager()): ?>
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus karyawan <strong id="employeeName"></strong>?</p>
                <p class="text-danger"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Managemen\resources\views/employees/index.blade.php ENDPATH**/ ?>