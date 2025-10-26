

<?php $__env->startSection('title', 'Master Gudang'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-warehouse"></i> Master Gudang</h1>
        <a href="<?php echo e(route(auth()->user()->isManager() ? 'manager.gudangs.create' : 'admin.gudangs.create')); ?>" class="btn btn-primary">
            <i class="bi bi-person-plus"></i> Tambah Karyawan Gudang
        </a>
</div>

<!-- Search Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route(auth()->user()->isManager() ? 'manager.gudangs.index' : 'admin.gudangs.index')); ?>">
            <div class="row g-3">
                <div class="col-md-8">
                    <label for="search" class="form-label">Cari Karyawan Gudang</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="<?php echo e(request('search')); ?>" placeholder="Masukkan nama karyawan gudang">
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Gudangs Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Daftar Gudang</h5>
    </div>
    <div class="card-body">
        <?php if($gudangs->count() > 0): ?>
            <div class="table-responsive" style="max-height: 500px; overflow-y: auto; overflow-x: auto;">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Karyawan Gudang</th>
                            <th>Gaji</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $gudangs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $gudang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($gudangs->firstItem() + $index); ?></td>
                                <td><?php echo e($gudang->nama); ?></td>
                                <td>
                                    <span class="text-success fw-bold">
                                        Rp <?php echo e(number_format($gudang->gaji, 0, ',', '.')); ?>

                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?php echo e(route(auth()->user()->isManager() ? 'manager.gudangs.show' : 'admin.gudangs.show', $gudang)); ?>"
                                           class="btn btn-info btn-sm" title="Lihat">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route(auth()->user()->isManager() ? 'manager.gudangs.edit' : 'admin.gudangs.edit', $gudang)); ?>"
                                           class="btn btn-warning btn-sm" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if(auth()->user()->isManager()): ?>
                                            <button type="button" class="btn btn-danger btn-sm"
                                                    data-item-id="<?php echo e($gudang->id); ?>" data-item-name="<?php echo e($gudang->nama); ?>" onclick="confirmDelete(this)" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <!-- Results Info -->
            <div class="d-flex justify-content-center mt-3">
                <small class="text-muted">Menampilkan <?php echo e($gudangs->count()); ?> dari <?php echo e($gudangs->total()); ?> data gudang</small>
            </div>
        <?php else: ?>
            <div class="alert alert-info" role="alert">
                Tidak ada data gudang yang ditemukan.
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
                        <h6 class="card-title">Total Karyawan Gudang</h6>
                        <h4 class="mb-0"><?php echo e(\App\Models\Gudang::count()); ?></h4>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-person-fill fa-2x"></i>
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
                <p>Apakah Anda yakin ingin menghapus gudang <strong id="gudangName"></strong>?</p>
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

<?php $__env->startPush('scripts'); ?>
<script>
    function confirmDelete(button) {
        document.getElementById('gudangName').textContent = name;
        document.getElementById('deleteForm').action = '<?php echo e(route("manager.gudangs.destroy", ":id")); ?>'.replace(':id', id);
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Managemen\resources\views/gudangs/index.blade.php ENDPATH**/ ?>