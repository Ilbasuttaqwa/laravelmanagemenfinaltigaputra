@extends('layouts.app')

@section('title', 'Tambah Absensi')

<!-- Force browser cache clear -->
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-plus-circle"></i> Tambah Absensi</h1>
    <a href="{{ route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h5 class="card-title mb-0">Form Tambah Absensi</h5>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Ada masalah:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form method="POST" action="{{ route(auth()->user()->isManager() ? 'manager.absensis.store' : 'admin.absensis.store') }}">
            @csrf

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="employee_id" class="form-label">Karyawan <span class="text-danger">*</span></label>
                    <select class="form-control @error('employee_id') is-invalid @enderror"
                            id="employee_id" name="employee_id" required>
                        <option value="">Pilih Karyawan</option>
                        @if(isset($allEmployees) && $allEmployees->count() > 0)
                            @foreach($allEmployees as $employee)
                                <option value="{{ $employee->id }}" 
                                        data-jabatan="{{ $employee->jabatan }}"
                                        data-gaji="{{ $employee->gaji_pokok }}"
                                        data-source="{{ $employee->source ?? 'unknown' }}"
                                        {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->nama }} ({{ $employee->jabatan === 'karyawan' ? 'karyawan kandang' : $employee->jabatan }})
                                </option>
                            @endforeach
                        @else
                            <option value="" disabled>Data karyawan tidak tersedia. Silakan refresh halaman.</option>
                        @endif
                    </select>
                    
                    <!-- Debug Info -->
                    @error('employee_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    
                    @if(config('app.debug'))
                        <small class="text-muted">
                            Debug: Total karyawan: {{ isset($allEmployees) ? $allEmployees->count() : 0 }}
                        </small>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="pembibitan_id" class="form-label">Pembibitan</label>
                    <select class="form-control @error('pembibitan_id') is-invalid @enderror"
                            id="pembibitan_id" name="pembibitan_id">
                        <option value="">Pilih Pembibitan</option>
                        @foreach($pembibitans as $pembibitan)
                            <option value="{{ $pembibitan->id }}" {{ old('pembibitan_id') == $pembibitan->id ? 'selected' : '' }}>
                                {{ $pembibitan->judul }} - {{ $pembibitan->kandang->nama_kandang ?? 'Tidak ada kandang' }} ({{ $pembibitan->lokasi->nama_lokasi ?? 'Tidak ada lokasi' }})
                            </option>
                        @endforeach
                    </select>
                    @error('pembibitan_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('tanggal') is-invalid @enderror"
                           id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                    @error('tanggal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" id="status_full" value="full" {{ old('status') == 'full' ? 'checked' : '' }}>
                        <label class="form-check-label" for="status_full">Full Day</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" id="status_half" value="setengah_hari" {{ old('status') == 'setengah_hari' ? 'checked' : '' }}>
                        <label class="form-check-label" for="status_half">½ Hari</label>
                    </div>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
                <a href="{{ route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index') }}"
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
@endsection

@push('scripts')
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
                // Ambil gaji dari data attribute (lebih cepat dan reliable)
                const gaji = parseFloat(selectedOption.getAttribute('data-gaji')) || 0;
                console.log('Gaji from data attribute:', gaji);
                
                if (gajiPokokDisplay && gajiPokokInput) {
                    gajiPokokDisplay.value = formatCurrency(gaji);
                    gajiPokokInput.value = gaji;
                    console.log('Gaji pokok filled:', gaji);
                }
                
                // Hitung gaji hari itu
                calculateGajiHariItu();
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
                window.location.href = '{{ route("admin.absensis.index") }}';
                
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

// Auto-clear cache on page load - DISABLED to prevent continuous refresh
// setTimeout(forceClearCache, 1000);

// Auto-refresh employee data if empty
function checkAndRefreshEmployeeData() {
    const employeeSelect = document.getElementById('employee_id');
    if (employeeSelect && employeeSelect.options.length <= 1) {
        console.log('⚠️ No employee data found, refreshing...');
        // Add loading indicator
        employeeSelect.innerHTML = '<option value="">Memuat data karyawan...</option>';
        
        // Refresh the page after 2 seconds
        setTimeout(() => {
            window.location.reload();
        }, 2000);
    }
}

// Check on page load
document.addEventListener('DOMContentLoaded', function() {
    checkAndRefreshEmployeeData();
});
</script>
@endpush
