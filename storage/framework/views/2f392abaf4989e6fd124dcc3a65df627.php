

<?php $__env->startSection('title', 'Edit Karyawan Kandang'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Karyawan Kandang</h1>
        <a href="<?php echo e(route(auth()->user()->isAdmin() ? 'admin.karyawan-kandangs.index' : 'manager.karyawan-kandangs.index')); ?>"
           class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i>
            Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Form Edit Karyawan Kandang</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="<?php echo e(route(auth()->user()->isAdmin() ? 'admin.karyawan-kandangs.update' : 'manager.karyawan-kandangs.update', $karyawanKandang)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Karyawan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?php $__errorArgs = ['nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           id="nama" name="nama" value="<?php echo e(old('nama', $karyawanKandang->nama)); ?>" required>
                    <?php $__errorArgs = ['nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="mb-3">
                    <label for="gaji_pokok" class="form-label">Gaji Pokok <span class="text-danger">*</span></label>
                    <input type="number" class="form-control <?php $__errorArgs = ['gaji_pokok'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           id="gaji_pokok" name="gaji_pokok" value="<?php echo e(old('gaji_pokok', $karyawanKandang->gaji_pokok)); ?>" 
                           min="0" step="1000" required>
                    <?php $__errorArgs = ['gaji_pokok'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="mb-3">
                    <label for="pembibitan_id" class="form-label">Pembibitan <span class="text-danger">*</span></label>
                    <select class="form-control <?php $__errorArgs = ['pembibitan_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            id="pembibitan_id" name="pembibitan_id" required>
                        <option value="">Pilih Pembibitan</option>
                        <?php $__currentLoopData = $pembibitans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pembibitan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($pembibitan->id); ?>" <?php echo e(old('pembibitan_id', $karyawanKandang->kandang_id) == $pembibitan->kandang_id ? 'selected' : ''); ?>>
                                <?php echo e($pembibitan->judul); ?> - <?php echo e($pembibitan->kandang->nama_kandang); ?> (<?php echo e($pembibitan->kandang->lokasi->nama_lokasi); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['pembibitan_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="<?php echo e(route(auth()->user()->isAdmin() ? 'admin.karyawan-kandangs.index' : 'manager.karyawan-kandangs.index')); ?>"
                       class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i>
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i>
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Managemen\resources\views/karyawan-kandangs/edit.blade.php ENDPATH**/ ?>