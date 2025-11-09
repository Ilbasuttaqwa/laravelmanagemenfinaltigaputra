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
                            <option value="" disabled>Tidak ada data karyawan</option>
                        @endif
                    </select>
                    
                    <!-- Debug Info -->
                    @error('employee_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    @if(config('app.debug') || true)
                        <small class="text-muted d-block mt-1">
                            <strong>Info:</strong> Total karyawan tersedia: {{ isset($allEmployees) ? $allEmployees->count() : 0 }}
                            @if(isset($allEmployees) && $allEmployees->count() > 0)
                                | Tipe:
                                @php
                                    $sources = $allEmployees->groupBy('source');
                                @endphp
                                @foreach($sources as $source => $items)
                                    {{ $source }}: {{ $items->count() }}{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            @endif
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
                    <label for="gaji_pokok_saat_itu_display" class="form-label">Gaji Pokok / Bulan</label>
                    <input type="text" class="form-control bg-light" id="gaji_pokok_saat_itu_display" readonly>
                    <input type="hidden" id="gaji_pokok_saat_itu" name="gaji_pokok_saat_itu">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="gaji_perhari_mentah_display" class="form-label">
                        Gaji Perhari (Perhitungan)
                        <small class="text-muted">(Gaji / 30 hari)</small>
                    </label>
                    <input type="text" class="form-control bg-light" id="gaji_perhari_mentah_display" readonly>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="pembulatan_option" class="form-label">
                        Pembulatan Gaji
                        <i class="bi bi-info-circle text-muted" title="Bulatkan gaji untuk memudahkan pembayaran"></i>
                    </label>
                    <select class="form-control" id="pembulatan_option">
                        <option value="1000">Bulatkan ke Rp 1.000</option>
                        <option value="5000">Bulatkan ke Rp 5.000</option>
                        <option value="10000" selected>Bulatkan ke Rp 10.000</option>
                        <option value="50000">Bulatkan ke Rp 50.000</option>
                        <option value="100000">Bulatkan ke Rp 100.000</option>
                        <option value="0">Tidak Dibulatkan</option>
                    </select>
                    <small class="text-muted">Pilih opsi pembulatan yang diinginkan</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="gaji_hari_itu_display" class="form-label">
                        <strong>Gaji Yang Dibayarkan</strong>
                        <span class="badge bg-success ms-1">Final</span>
                    </label>
                    <input type="text" class="form-control form-control-lg fw-bold text-success bg-light" id="gaji_hari_itu_display" readonly>
                    <input type="hidden" id="gaji_hari_itu" name="gaji_hari_itu">
                    <small class="text-muted">Nilai ini yang akan disimpan dan dibayarkan</small>
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
// Production-ready auto-fill script with fallbacks
(function() {
    'use strict';

    // Ensure jQuery is loaded
    function initializeForm() {
        console.log('🚀 Absensi form script loaded - Version 3.1 (With Smart Rounding)');
        console.log('📅 Current date:', new Date().toISOString());
        console.log('✨ NEW: Gaji pembulatan otomatis untuk kemudahan pembayaran');

    const employeeSelect = document.getElementById('employee_id');
    const gajiPokokDisplay = document.getElementById('gaji_pokok_saat_itu_display');
    const gajiPokokInput = document.getElementById('gaji_pokok_saat_itu');
    const gajiPerhariMentahDisplay = document.getElementById('gaji_perhari_mentah_display');
    const gajiHariItuDisplay = document.getElementById('gaji_hari_itu_display');
    const gajiHariItuInput = document.getElementById('gaji_hari_itu');
    const pembulatanOption = document.getElementById('pembulatan_option');
    const statusRadios = document.querySelectorAll('input[name="status"]');

    console.log('✅ Elements found:', {
        employeeSelect: !!employeeSelect,
        gajiPokokDisplay: !!gajiPokokDisplay,
        gajiPokokInput: !!gajiPokokInput,
        gajiPerhariMentahDisplay: !!gajiPerhariMentahDisplay,
        gajiHariItuDisplay: !!gajiHariItuDisplay,
        gajiHariItuInput: !!gajiHariItuInput,
        pembulatanOption: !!pembulatanOption,
        statusRadioCount: statusRadios.length
    });

    // Log employee dropdown options
    if (employeeSelect) {
        console.log('👥 Total employees in dropdown:', employeeSelect.options.length - 1); // -1 for "Pilih Karyawan" option
        console.log('📋 Employee options:', Array.from(employeeSelect.options).slice(1).map(opt => ({
            id: opt.value,
            nama: opt.textContent,
            gaji: opt.getAttribute('data-gaji'),
            source: opt.getAttribute('data-source')
        })));
    }

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

    // Hitung ulang saat opsi pembulatan berubah
    if (pembulatanOption) {
        pembulatanOption.addEventListener('change', calculateGajiHariItu);
    }

    function calculateGajiHariItu() {
        console.log('📊 Calculating gaji hari itu dengan pembulatan...');

        if (!employeeSelect || !employeeSelect.value) {
            console.log('❌ No employee selected');
            return;
        }

        const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
        if (!selectedOption || !selectedOption.value) {
            console.log('❌ No valid employee option selected');
            return;
        }

        const gajiPokok = parseFloat(selectedOption.getAttribute('data-gaji')) || 0;
        const selectedStatus = document.querySelector('input[name="status"]:checked');

        console.log('💰 Gaji pokok:', formatCurrency(gajiPokok));
        console.log('📝 Selected status:', selectedStatus ? selectedStatus.value : 'none');

        if (!selectedStatus) {
            console.log('⚠️ No status selected yet - waiting for user to select status');
            // Clear all gaji fields
            if (gajiPerhariMentahDisplay) gajiPerhariMentahDisplay.value = '';
            if (gajiHariItuDisplay) gajiHariItuDisplay.value = '';
            if (gajiHariItuInput) gajiHariItuInput.value = '';
            return;
        }

        if (gajiPokok === 0) {
            console.log('⚠️ Gaji pokok is 0 - cannot calculate');
            return;
        }

        // Hitung gaji perhari mentah (exact calculation)
        let gajiPerhariMentah = 0;
        if (selectedStatus.value === 'full') {
            gajiPerhariMentah = gajiPokok / 30; // Gaji per hari
            console.log('✅ Full day: Rp', formatNumber(gajiPokok), '/ 30 hari = Rp', formatNumber(gajiPerhariMentah));
        } else if (selectedStatus.value === 'setengah_hari') {
            gajiPerhariMentah = (gajiPokok / 30) / 2; // Setengah hari
            console.log('✅ Half day: (Rp', formatNumber(gajiPokok), '/ 30 hari) / 2 = Rp', formatNumber(gajiPerhariMentah));
        }

        // Tampilkan gaji mentah (belum dibulatkan)
        if (gajiPerhariMentahDisplay) {
            gajiPerhariMentahDisplay.value = formatCurrency(gajiPerhariMentah);
        }

        // Bulatkan gaji berdasarkan opsi yang dipilih
        const pembulatanValue = pembulatanOption ? parseInt(pembulatanOption.value) : 10000;
        let gajiFinal = gajiPerhariMentah;

        if (pembulatanValue > 0) {
            // Bulatkan ke atas (ceiling) ke kelipatan terdekat
            gajiFinal = Math.ceil(gajiPerhariMentah / pembulatanValue) * pembulatanValue;
            console.log('🔢 Pembulatan ke Rp', formatNumber(pembulatanValue), ': Rp', formatNumber(gajiPerhariMentah), '→ Rp', formatNumber(gajiFinal));
        } else {
            console.log('🔢 Tidak dibulatkan, menggunakan nilai exact: Rp', formatNumber(gajiFinal));
        }

        // Tampilkan gaji final (yang akan dibayarkan)
        if (gajiHariItuDisplay && gajiHariItuInput) {
            gajiHariItuDisplay.value = formatCurrency(gajiFinal);
            gajiHariItuInput.value = gajiFinal.toFixed(2);
            console.log('✅ Gaji yang dibayarkan (final): Rp', formatNumber(gajiFinal));
        }

        // Show summary in console
        console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        console.log('📊 RINGKASAN PERHITUNGAN:');
        console.log('   Gaji Pokok/Bulan : Rp', formatNumber(gajiPokok));
        console.log('   Status           :', selectedStatus.value === 'full' ? 'Full Day' : 'Setengah Hari');
        console.log('   Gaji Perhitungan : Rp', formatNumber(gajiPerhariMentah));
        console.log('   Pembulatan       : Rp', formatNumber(pembulatanValue));
        console.log('   💰 GAJI DIBAYAR  : Rp', formatNumber(gajiFinal));
        console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }

    function formatNumber(number) {
        return new Intl.NumberFormat('id-ID').format(number);
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
            
            const pembulatanOpt = document.getElementById('pembulatan_option');

            console.log('📊 Form Data:', {
                employeeId,
                tanggal,
                status: status ? status.value : 'none',
                gajiPokok,
                gajiHariItu,
                pembulatan: pembulatanOpt ? pembulatanOpt.value : '10000'
            });

            if (!employeeId || !tanggal || !status || !gajiPokok || !gajiHariItu) {
                console.error('❌ Missing required fields');
                alert('Mohon lengkapi semua field yang diperlukan:\n- Pilih Karyawan\n- Pilih Tanggal\n- Pilih Status (Full Day / Setengah Hari)\n- Gaji otomatis akan terisi');
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
                
                const result = await response.json();
                console.log('Response:', result);
                
                if (response.ok && result.success) {
                    // Show success message
                    alert('Data absensi berhasil disimpan!');
                    window.location.href = '{{ route(auth()->user()->isManager() ? "manager.absensis.index" : "admin.absensis.index") }}';
                } else {
                    // Handle error response
                    let errorMessage = 'Terjadi kesalahan saat menyimpan data';
                    if (result.message) {
                        errorMessage = result.message;
                    } else if (response.status === 409) {
                        errorMessage = 'Data absensi untuk karyawan ini pada tanggal tersebut sudah ada. Silakan pilih tanggal lain atau cek data yang sudah ada.';
                    } else if (response.status === 422) {
                        errorMessage = 'Data yang dimasukkan tidak valid. Silakan periksa kembali.';
                    }
                    
                    alert('Error: ' + errorMessage);
                    
                    // Restore button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
                
            } catch (error) {
                console.error('❌ Form submission error:', error);
                
                let errorMessage = 'Terjadi kesalahan: ' + error.message;
                if (error.message.includes('409')) {
                    errorMessage = 'Data absensi untuk karyawan ini pada tanggal tersebut sudah ada. Silakan pilih tanggal lain.';
                }
                
                alert(errorMessage);
                
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

// Employee dropdown is now loaded directly from server
// No need for dynamic loading based on pembibitan

    // Employee dropdown is now loaded directly from server
    // No need for dynamic loading based on pembibitan
    } // End initializeForm

    // Initialize with jQuery if available, otherwise use vanilla JS
    if (typeof jQuery !== 'undefined') {
        jQuery(document).ready(initializeForm);
    } else if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeForm);
    } else {
        initializeForm();
    }
})(); // End IIFE
</script>
@endpush
