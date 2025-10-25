<?php $__env->startSection('title', 'Tambah Absensi'); ?>

<!-- Force browser cache clear -->
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-plus-circle"></i> Tambah Absensi</h1>
    <a href="<?php echo e(route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index')); ?>" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h5 class="card-title mb-0">Form Tambah Absensi</h5>
    </div>
    <div class="card-body">
        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <strong>Ada masalah:</strong>
                <ul class="mb-0">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo e(route(auth()->user()->isManager() ? 'manager.absensis.store' : 'admin.absensis.store')); ?>">
            <?php echo csrf_field(); ?>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="employee_id" class="form-label">Karyawan <span class="text-danger">*</span></label>
                    <select class="form-control <?php $__errorArgs = ['employee_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            id="employee_id" name="employee_id" required>
                        <option value="">Pilih Karyawan</option>
                        <?php $__currentLoopData = $allEmployees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($employee->id); ?>" 
                                    data-jabatan="<?php echo e($employee->jabatan); ?>"
                                    data-gaji="<?php echo e($employee->gaji_pokok); ?>"
                                    data-source="<?php echo e($employee->source ?? 'unknown'); ?>"
                                    <?php echo e(old('employee_id') == $employee->id ? 'selected' : ''); ?>>
                                <?php echo e($employee->nama); ?> (<?php echo e($employee->jabatan); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    
                    <!-- Debug Info -->
                    <small class="text-muted">
                        Total employees: <?php echo e(count($allEmployees)); ?> | 
                        Gudang: <?php echo e($allEmployees->where('source', 'gudang')->count()); ?> | 
                        Regular: <?php echo e($allEmployees->where('source', 'employee')->count()); ?>

                    </small>
                    <?php $__errorArgs = ['employee_id'];
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
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="pembibitan_id" class="form-label">Pembibitan</label>
                    <select class="form-control <?php $__errorArgs = ['pembibitan_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            id="pembibitan_id" name="pembibitan_id">
                        <option value="">Pilih Pembibitan</option>
                        <?php $__currentLoopData = $pembibitans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pembibitan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($pembibitan->id); ?>" <?php echo e(old('pembibitan_id') == $pembibitan->id ? 'selected' : ''); ?>>
                                <?php echo e($pembibitan->judul); ?> - <?php echo e($pembibitan->kandang->nama_kandang ?? 'Tidak ada kandang'); ?> (<?php echo e($pembibitan->lokasi->nama_lokasi ?? 'Tidak ada lokasi'); ?>)
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
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" class="form-control <?php $__errorArgs = ['tanggal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           id="tanggal" name="tanggal" value="<?php echo e(old('tanggal', date('Y-m-d'))); ?>" required>
                    <?php $__errorArgs = ['tanggal'];
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

                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" id="status_full" value="full" <?php echo e(old('status') == 'full' ? 'checked' : ''); ?>>
                        <label class="form-check-label" for="status_full">Full Day</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" id="status_half" value="setengah_hari" <?php echo e(old('status') == 'setengah_hari' ? 'checked' : ''); ?>>
                        <label class="form-check-label" for="status_half">½ Hari</label>
                    </div>
                    <?php $__errorArgs = ['status'];
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
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="gaji_pokok_saat_itu_display" class="form-label">Gaji Pokok</label>
                    <input type="text" class="form-control" id="gaji_pokok_saat_itu_display" readonly>
                    <input type="hidden" id="gaji_pokok_saat_itu" name="gaji_pokok_saat_itu">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="gaji_hari_itu_display" class="form-label">Gaji Perhari</label>
                    <input type="text" class="form-control" id="gaji_hari_itu_display" readonly>
                    <input type="hidden" id="gaji_hari_itu" name="gaji_hari_itu">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="<?php echo e(route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index')); ?>"
                   class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Ensure jQuery is available
$(document).ready(function() {
    console.log('Absensi form script loaded');
    
    const employeeSelect = document.getElementById('employee_id');
    const gajiPokokDisplay = document.getElementById('gaji_pokok_saat_itu_display');
    const gajiPokokInput = document.getElementById('gaji_pokok_saat_itu');
    const gajiHariItuDisplay = document.getElementById('gaji_hari_itu_display');
    const gajiHariItuInput = document.getElementById('gaji_hari_itu');
    const statusRadios = document.querySelectorAll('input[name="status"]');

    console.log('Elements found:', {
        employeeSelect: !!employeeSelect,
        gajiPokokDisplay: !!gajiPokokDisplay,
        gajiPokokInput: !!gajiPokokInput,
        gajiHariItuDisplay: !!gajiHariItuDisplay,
        gajiHariItuInput: !!gajiHariItuInput
    });

    // Trigger auto-fill on page load if employee is already selected
    if (employeeSelect && employeeSelect.value) {
        console.log('Employee already selected:', employeeSelect.value);
        const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
        if (selectedOption.value) {
            const gaji = parseFloat(selectedOption.getAttribute('data-gaji')) || 0;
            console.log('Gaji from data attribute:', gaji);
            
            if (gajiPokokDisplay && gajiPokokInput) {
                gajiPokokDisplay.value = formatCurrency(gaji);
                gajiPokokInput.value = gaji;
                console.log('Gaji pokok filled:', gajiPokokInput.value);
            }
            
            // Calculate gaji hari itu if status is selected
            calculateGajiHariItu();
        }
    }

    // Auto-fill gaji pokok saat pilih karyawan
    if (employeeSelect) {
        employeeSelect.addEventListener('change', function() {
            console.log('Employee changed to:', this.value);
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                // Ambil gaji terbaru dari server secara real-time
                fetchLatestSalary(this.value);
            } else {
                if (gajiPokokDisplay && gajiPokokInput) {
                    gajiPokokDisplay.value = '';
                    gajiPokokInput.value = '';
                }
                if (gajiHariItuDisplay && gajiHariItuInput) {
                    gajiHariItuDisplay.value = '';
                    gajiHariItuInput.value = '';
                }
            }
        });
    }

    // Function to fetch latest salary from server
    function fetchLatestSalary(employeeId) {
        fetch('/manager/absensis/get-salary/' + employeeId)
            .then(response => response.json())
            .then(data => {
                console.log('Latest salary from server:', data.gaji);
                const gaji = parseFloat(data.gaji) || 0;
                
                if (gajiPokokDisplay && gajiPokokInput) {
                    gajiPokokDisplay.value = formatCurrency(gaji);
                    gajiPokokInput.value = gaji;
                    console.log('Gaji pokok filled with latest data:', gajiPokokInput.value);
                }
                
                // Hitung gaji hari itu berdasarkan status
                calculateGajiHariItu();
            })
            .catch(error => {
                console.error('Error fetching salary:', error);
                // Fallback to data attribute if API fails
                const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
                const gaji = parseFloat(selectedOption.getAttribute('data-gaji')) || 0;
                if (gajiPokokDisplay && gajiPokokInput) {
                    gajiPokokDisplay.value = formatCurrency(gaji);
                    gajiPokokInput.value = gaji;
                }
                calculateGajiHariItu();
            });
    }

    // Hitung gaji hari itu saat status berubah
    statusRadios.forEach(radio => {
        radio.addEventListener('change', calculateGajiHariItu);
    });

    function calculateGajiHariItu() {
        console.log('Calculating gaji hari itu...');
        const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
        if (!selectedOption.value) {
            console.log('No employee selected');
            return;
        }

        const gajiPokok = parseFloat(selectedOption.getAttribute('data-gaji')) || 0;
        const selectedStatus = document.querySelector('input[name="status"]:checked');
        
        console.log('Gaji pokok:', gajiPokok);
        console.log('Selected status:', selectedStatus ? selectedStatus.value : 'none');
        
        if (selectedStatus && gajiHariItuDisplay && gajiHariItuInput) {
            let gajiHariItu = 0;
            if (selectedStatus.value === 'full') {
                gajiHariItu = gajiPokok / 30; // Gaji per hari
            } else if (selectedStatus.value === 'setengah_hari') {
                gajiHariItu = (gajiPokok / 30) / 2; // Setengah hari
            }
            
            console.log('Calculated gaji hari itu:', gajiHariItu);
            gajiHariItuDisplay.value = formatCurrency(gajiHariItu);
            gajiHariItuInput.value = gajiHariItu.toFixed(2); // Simpan nilai numerik dengan 2 desimal
            console.log('Gaji hari itu input filled:', gajiHariItuInput.value);
        }
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }

    // Simple form submission with validation
    const form = document.querySelector('form[action*="absensis"]');
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            console.log('🚀 Form submit triggered');
            
            // Check if required fields are filled
            const employeeId = document.getElementById('employee_id').value;
            const tanggal = document.getElementById('tanggal').value;
            const status = document.querySelector('input[name="status"]:checked');
            const gajiPokok = document.getElementById('gaji_pokok_saat_itu').value;
            const gajiHariItu = document.getElementById('gaji_hari_itu').value;
            
            console.log('📊 Form Data:', {
                employeeId,
                tanggal,
                status: status ? status.value : 'none',
                gajiPokok,
                gajiHariItu
            });
            
            if (!employeeId || !tanggal || !status || !gajiPokok || !gajiHariItu) {
                console.error('❌ Missing required fields');
                alert('Mohon lengkapi semua field yang diperlukan');
                return false;
            }
            
            console.log('✅ Form validation passed, submitting...');
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Menyimpan...';
            
            try {
                const formData = new FormData(form);
                
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const result = await response.json();
                console.log('✅ Form submitted successfully:', result);
                
                // Show success message
                alert('Data absensi berhasil disimpan!');
                window.location.href = '<?php echo e(route("admin.absensis.index")); ?>';
                
            } catch (error) {
                console.error('❌ Form submission error:', error);
                alert('Terjadi kesalahan: ' + error.message);
                
                // Restore button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }
});

// Force clear browser cache and refresh data
function forceClearCache() {
    console.log('🧹 Force clearing browser cache...');
    
    // Clear service worker cache
    if ('caches' in window) {
        caches.keys().then(function(names) {
            for (let name of names) {
                caches.delete(name);
            }
            console.log('✅ Service worker cache cleared');
        });
    }
    
    // Clear localStorage
    localStorage.clear();
    console.log('✅ LocalStorage cleared');
    
    // Clear sessionStorage
    sessionStorage.clear();
    console.log('✅ SessionStorage cleared');
    
    // Force reload page with cache busting
    const timestamp = new Date().getTime();
    window.location.href = window.location.href + '?t=' + timestamp;
}

// Auto-clear cache on page load
setTimeout(forceClearCache, 1000);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Managemen\resources\views/absensis/create.blade.php ENDPATH**/ ?>