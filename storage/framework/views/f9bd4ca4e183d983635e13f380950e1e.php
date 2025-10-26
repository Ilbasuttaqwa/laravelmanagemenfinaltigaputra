

<?php $__env->startSection('title', 'Master Karyawan Kandang'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-people-fill"></i>
            Master Karyawan Kandang
        </h1>
        <a href="<?php echo e(route(auth()->user()->isAdmin() ? 'admin.karyawan-kandangs.create' : 'manager.karyawan-kandangs.create')); ?>"
           class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            Tambah Karyawan Kandang
        </a>
    </div>

    <!-- Search Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route(auth()->user()->isAdmin() ? 'admin.karyawan-kandangs.index' : 'manager.karyawan-kandangs.index')); ?>">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label for="search" class="form-label">Cari Karyawan</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="<?php echo e(request('search')); ?>" placeholder="Masukkan nama karyawan">
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

    <!-- Karyawan Kandang Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Daftar Karyawan Kandang</h5>
        </div>
        <div class="card-body">
            <?php if($karyawans->count() > 0): ?>
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto; overflow-x: auto;">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Karyawan</th>
                                <th>Gaji Pokok</th>
                                <th>Kandang</th>
                                <th>Lokasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $karyawans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $karyawan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($karyawans->firstItem() + $index); ?></td>
                                    <td>
                                        <strong><?php echo e($karyawan->nama); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Rp <?php echo e(number_format($karyawan->gaji_pokok, 0, ',', '.')); ?></span>
                                    </td>
                                    <td>
                                        <?php if($karyawan->kandang): ?>
                                            <span class="badge bg-info"><?php echo e($karyawan->kandang->nama_kandang); ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Belum ada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($karyawan->kandang && $karyawan->kandang->lokasi): ?>
                                            <span class="badge bg-primary"><?php echo e($karyawan->kandang->lokasi->nama_lokasi); ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Belum ada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="<?php echo e(route(auth()->user()->isManager() ? 'manager.karyawan-kandangs.show' : 'admin.karyawan-kandangs.show', $karyawan)); ?>"
                                               class="btn btn-info btn-sm" title="Lihat">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route(auth()->user()->isManager() ? 'manager.karyawan-kandangs.edit' : 'admin.karyawan-kandangs.edit', $karyawan)); ?>"
                                               class="btn btn-warning btn-sm" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <?php if(auth()->user()->isManager()): ?>
                                                <form action="<?php echo e(route('manager.karyawan-kandangs.destroy', $karyawan)); ?>" method="POST" 
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus karyawan ini?')" class="d-inline">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Menampilkan <?php echo e($karyawans->firstItem()); ?> sampai <?php echo e($karyawans->lastItem()); ?> dari <?php echo e($karyawans->total()); ?> data
                    </div>
                    <div>
                        <?php echo e($karyawans->links()); ?>

                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="bi bi-people display-1 text-muted"></i>
                    <h5 class="text-muted mt-3">Belum ada data karyawan kandang</h5>
                    <p class="text-muted">Silakan tambah data karyawan kandang terlebih dahulu.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Managemen\resources\views/karyawan-kandangs/index.blade.php ENDPATH**/ ?>