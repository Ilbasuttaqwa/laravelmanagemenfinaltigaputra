@extends('layouts.app')

@section('title', 'Tambah Absensi')

<!-- Force browser cache clear - Version 3.3 -->
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta name="script-version" content="3.3-<?php echo time(); ?>">

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="bi bi-plus-circle"></i> Tambah Absensi</h1>
    <a href="{{ route(auth()->user()->isManager() ? 'manager.absensis.index' : 'admin.absensis.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="row">
    <!-- Form Absensi -->
    <div class="col-md-8">
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

                <form method="POST" action="{{ route(auth()->user()->isManager() ? 'manager.absensis.store' : 'admin.absensis.store') }}" id="absensiForm">
                    @csrf

                    <!-- Filter Tabs untuk Karyawan -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Filter Karyawan Berdasarkan Jabatan</label>
                        <ul class="nav nav-pills mb-3" id="employeeFilterTabs">
                            <li class="nav-item">
                                <button class="nav-link active" type="button" data-filter="all">
                                    <i class="bi bi-people-fill"></i> Semua
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" type="button" data-filter="karyawan">
                                    <i class="bi bi-house"></i> Karyawan Kandang
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" type="button" data-filter="karyawan_gudang">
                                    <i class="bi bi-building"></i> Karyawan Gudang
                                </button>
                            </li>
                            @if(auth()->user()->isManager())
                            <li class="nav-item">
                                <button class="nav-link" type="button" data-filter="mandor">
                                    <i class="bi bi-person-badge"></i> Mandor
                                </button>
                            </li>
                            @endif
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="employee_id" class="form-label">Pilih Karyawan <span class="text-danger">*</span></label>
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
                                            {{ $employee->nama }} ({{ $employee->jabatan === 'karyawan' ? 'Kandang' : ($employee->jabatan === 'karyawan_gudang' ? 'Gudang' : ucfirst($employee->jabatan)) }})
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>Tidak ada data karyawan</option>
                                @endif
                            </select>

                            @error('employee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <small class="text-muted d-block mt-1">
                                <i class="bi bi-info-circle"></i> Filter jabatan di atas untuk memudahkan pencarian
                            </small>
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
                <button type="submit" class="btn btn-primary" id="submitButton">
                    <i class="bi bi-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
    </div> <!-- End col-md-8 -->

    <!-- Riwayat Absensi Hari Ini -->
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-info text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history"></i> Riwayat Absensi Hari Ini
                </h5>
            </div>
            <div class="card-body">
                <div id="riwayatAbsensiContainer">
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-person-circle" style="font-size: 3rem;"></i>
                        <p class="mt-2">Pilih karyawan untuk melihat riwayat absensi hari ini</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Business Rules Info -->
        <div class="card shadow border-warning">
            <div class="card-header py-2 bg-warning bg-opacity-10">
                <h6 class="mb-0">
                    <i class="bi bi-exclamation-triangle text-warning"></i> Aturan Absensi
                </h6>
            </div>
            <div class="card-body p-3">
                <ul class="mb-0 small">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success"></i>
                        <strong>Boleh</strong> absen 2x di <strong>pembibitan berbeda</strong>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-x-circle text-danger"></i>
                        <strong>Tidak boleh</strong> absen 2x di <strong>pembibitan yang sama</strong>
                    </li>
                    <li>
                        <i class="bi bi-info-circle text-info"></i>
                        Contoh: Full day di Pembibitan A, lalu ½ hari di Pembibitan B = <span class="text-success">OK</span>
                    </li>
                </ul>
            </div>
        </div>
    </div> <!-- End col-md-4 -->
</div> <!-- End row -->
@endsection

@push('scripts')
<script>
// Production-ready auto-fill script with fallbacks
(function() {
    'use strict';

    // Ensure jQuery is loaded
    function initializeForm() {
        console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        console.log('🚀 Absensi Form Script - Version 3.3 (SPACE SEPARATOR)');
        console.log('📅 Loaded at:', new Date().toISOString());
        console.log('✨ Currency Format: Rp 150 000 000 (SPASI, NO TITIK!)');
        console.log('🔧 Format Baru: SPASI sebagai separator, BUKAN TITIK');
        console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

    // ===== FORMAT FUNCTIONS (DEFINED FIRST) =====
    function formatNumber(number) {
        // Format angka dengan SPASI sebagai pemisah ribuan
        // Contoh: 150000000 → 150 000 000 (MUDAH DIBACA, NO TITIK!)
        const num = Math.round(number);
        // Convert to string and add space every 3 digits from the right
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    }

    function formatCurrency(amount) {
        // Format currency dengan spasi sebagai separator
        // Rp 150 000 000 (bukan Rp 150.000.000)
        return `Rp ${formatNumber(amount)}`;
    }

    function formatCurrencyCompact(amount) {
        // Format super compact untuk display yang sangat simple
        // 1500000 (tanpa Rp, tanpa titik)
        return Math.round(amount).toString();
    }

    // ===== DOM ELEMENTS =====
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
            jabatan: opt.getAttribute('data-jabatan'),
            gaji: opt.getAttribute('data-gaji'),
            source: opt.getAttribute('data-source')
        })));

        // CRITICAL: Detect employees with missing or invalid gaji
        const invalidGajiEmployees = Array.from(employeeSelect.options).slice(1).filter(opt => {
            const gaji = opt.getAttribute('data-gaji');
            return !gaji || gaji === '' || gaji === '0' || isNaN(parseFloat(gaji)) || parseFloat(gaji) <= 0;
        });

        if (invalidGajiEmployees.length > 0) {
            console.error('🚨 CRITICAL: Employees with missing/invalid gaji_pokok detected!');
            console.error('⚠️ These employees will NOT be able to calculate salary:');
            invalidGajiEmployees.forEach(opt => {
                console.error(`   ❌ ${opt.textContent} - gaji: "${opt.getAttribute('data-gaji')}" (source: ${opt.getAttribute('data-source')})`);
            });
            console.error('🔧 FIX: Update gaji_pokok in database for these employees!');
        } else {
            console.log('✅ All employees have valid gaji_pokok');
        }
    }

    // ===== FILTER TABS FUNCTIONALITY =====
    const filterTabs = document.querySelectorAll('#employeeFilterTabs button');
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const filter = this.getAttribute('data-filter');
            console.log('🔍 Filter changed to:', filter);
            filterEmployeeDropdown(filter);
        });
    });

    function filterEmployeeDropdown(jabatan) {
        if (!employeeSelect) return;

        const options = Array.from(employeeSelect.options);
        let visibleCount = 0;

        options.forEach(opt => {
            if (opt.value === '') {
                // Always show "Pilih Karyawan" option
                opt.style.display = '';
                return;
            }

            const optJabatan = opt.getAttribute('data-jabatan');
            if (jabatan === 'all' || optJabatan === jabatan) {
                opt.style.display = '';
                visibleCount++;
            } else {
                opt.style.display = 'none';
            }
        });

        console.log(`✅ Filter applied: ${jabatan}, visible: ${visibleCount} employees`);

        // Reset selection if current selection is hidden
        if (employeeSelect.value && employeeSelect.options[employeeSelect.selectedIndex].style.display === 'none') {
            employeeSelect.value = '';
            // Clear gaji fields
            if (gajiPokokDisplay) gajiPokokDisplay.value = '';
            if (gajiPokokInput) gajiPokokInput.value = '';
            if (gajiPerhariMentahDisplay) gajiPerhariMentahDisplay.value = '';
            if (gajiHariItuDisplay) gajiHariItuDisplay.value = '';
            if (gajiHariItuInput) gajiHariItuInput.value = '';
            // Clear riwayat
            clearRiwayatAbsensi();
        }
    }

    // ===== RIWAYAT ABSENSI FUNCTIONALITY =====
    function loadRiwayatAbsensi(employeeId) {
        if (!employeeId) {
            clearRiwayatAbsensi();
            return;
        }

        const container = document.getElementById('riwayatAbsensiContainer');
        if (!container) return;

        // Show loading
        container.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Memuat riwayat...</p>
            </div>
        `;

        const today = document.getElementById('tanggal').value || new Date().toISOString().split('T')[0];
        const rolePrefix = '{{ auth()->user()->isManager() ? "manager" : "admin" }}';
        const url = `/${rolePrefix}/absensis/riwayat/${employeeId}?tanggal=${today}`;

        console.log('📋 Loading riwayat for employee:', employeeId, 'date:', today);

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('✅ Riwayat loaded:', data);
            displayRiwayatAbsensi(data);
        })
        .catch(error => {
            console.error('❌ Error loading riwayat:', error);
            container.innerHTML = `
                <div class="alert alert-warning mb-0">
                    <i class="bi bi-exclamation-triangle"></i>
                    Gagal memuat riwayat absensi
                </div>
            `;
        });
    }

    function displayRiwayatAbsensi(data) {
        const container = document.getElementById('riwayatAbsensiContainer');
        if (!container) return;

        if (!data.riwayat || data.riwayat.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted py-3">
                    <i class="bi bi-calendar-check" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0">Belum ada absensi hari ini</p>
                    <small class="text-success">Aman untuk tambah absensi baru</small>
                </div>
            `;
            return;
        }

        let html = `
            <div class="alert alert-info mb-3 py-2 px-3">
                <strong><i class="bi bi-info-circle"></i> ${data.riwayat.length} absensi hari ini</strong>
            </div>
        `;

        data.riwayat.forEach((absensi, index) => {
            const statusBadge = absensi.status === 'full' ?
                '<span class="badge bg-primary">Full Day</span>' :
                '<span class="badge bg-warning">½ Hari</span>';

            html += `
                <div class="card mb-2 border-start border-3 ${absensi.status === 'full' ? 'border-primary' : 'border-warning'}">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="mb-1">Absensi #${index + 1}</h6>
                            ${statusBadge}
                        </div>
                        <p class="mb-1 small"><strong>Pembibitan:</strong> ${absensi.pembibitan || '-'}</p>
                        <p class="mb-1 small"><strong>Lokasi:</strong> ${absensi.lokasi || '-'}</p>
                        <p class="mb-0 small"><strong>Gaji:</strong> <span class="text-success fw-bold">Rp ${formatNumber(absensi.gaji_hari_itu)}</span></p>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;

        // Store riwayat for validation
        window.currentEmployeeRiwayat = data.riwayat;
    }

    function clearRiwayatAbsensi() {
        const container = document.getElementById('riwayatAbsensiContainer');
        if (!container) return;

        container.innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="bi bi-person-circle" style="font-size: 3rem;"></i>
                <p class="mt-2">Pilih karyawan untuk melihat riwayat absensi hari ini</p>
            </div>
        `;

        window.currentEmployeeRiwayat = null;
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
            console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            console.log('👤 Employee changed to:', this.value);
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                // Ambil gaji dari data attribute (lebih cepat dan reliable)
                const gajiAttr = selectedOption.getAttribute('data-gaji');
                const gaji = parseFloat(gajiAttr) || 0;
                const employeeName = selectedOption.textContent;
                const employeeSource = selectedOption.getAttribute('data-source');

                console.log('📊 Employee Details:');
                console.log('   Nama:', employeeName);
                console.log('   Source:', employeeSource);
                console.log('   Gaji raw attribute:', gajiAttr);
                console.log('   Gaji parsed:', gaji);

                // Validate gaji
                if (!gajiAttr || gajiAttr === '' || gajiAttr === '0' || gaji <= 0) {
                    console.error('❌ INVALID GAJI DETECTED for', employeeName);

                    // Clear all gaji fields
                    if (gajiPokokDisplay) gajiPokokDisplay.value = '';
                    if (gajiPokokInput) gajiPokokInput.value = '';
                    if (gajiPerhariMentahDisplay) gajiPerhariMentahDisplay.value = '';
                    if (gajiHariItuDisplay) gajiHariItuDisplay.value = '';
                    if (gajiHariItuInput) gajiHariItuInput.value = '';

                    // Show warning to user
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian!',
                            html: `
                                <p><strong>${employeeName}</strong> belum memiliki gaji pokok yang valid.</p>
                                <p class="small text-muted mt-2">
                                    <i class="bi bi-info-circle"></i>
                                    Silakan hubungi admin untuk mengatur gaji terlebih dahulu.
                                </p>
                            `,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#ffc107'
                        });
                    } else {
                        alert(`⚠️ ${employeeName} belum memiliki gaji pokok yang valid.\n\nSilakan hubungi admin untuk mengatur gaji terlebih dahulu.`);
                    }

                    // Still load riwayat but don't calculate gaji
                    loadRiwayatAbsensi(selectedOption.value);
                    return;
                }

                // Gaji valid, proceed with auto-fill
                if (gajiPokokDisplay && gajiPokokInput) {
                    gajiPokokDisplay.value = formatCurrency(gaji);
                    gajiPokokInput.value = gaji;
                    console.log('✅ Gaji pokok filled:', formatCurrency(gaji));
                }

                // Hitung gaji hari itu (will check if status is selected)
                calculateGajiHariItu();

                // LOAD RIWAYAT ABSENSI
                loadRiwayatAbsensi(selectedOption.value);
            } else {
                console.log('Employee cleared');
                // Clear all fields
                if (gajiPokokDisplay && gajiPokokInput) {
                    gajiPokokDisplay.value = '';
                    gajiPokokInput.value = '';
                }
                if (gajiPerhariMentahDisplay) gajiPerhariMentahDisplay.value = '';
                if (gajiHariItuDisplay && gajiHariItuInput) {
                    gajiHariItuDisplay.value = '';
                    gajiHariItuInput.value = '';
                }
                // Clear riwayat
                clearRiwayatAbsensi();
            }
            console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
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

        const gajiPokokAttr = selectedOption.getAttribute('data-gaji');
        const gajiPokok = parseFloat(gajiPokokAttr) || 0;
        const selectedStatus = document.querySelector('input[name="status"]:checked');

        console.log('💰 Gaji pokok raw:', gajiPokokAttr);
        console.log('💰 Gaji pokok parsed:', gajiPokok);
        console.log('📝 Selected status:', selectedStatus ? selectedStatus.value : 'none');

        if (!selectedStatus) {
            console.log('⚠️ No status selected yet - waiting for user to select status');
            // Clear calculation fields only (not gaji pokok)
            if (gajiPerhariMentahDisplay) gajiPerhariMentahDisplay.value = '';
            if (gajiHariItuDisplay) gajiHariItuDisplay.value = '';
            if (gajiHariItuInput) gajiHariItuInput.value = '';
            return;
        }

        // CRITICAL FIX: Proper validation with user feedback
        if (!gajiPokokAttr || gajiPokokAttr === '' || gajiPokokAttr === '0' || isNaN(gajiPokok) || gajiPokok <= 0) {
            const employeeName = selectedOption.textContent;
            const employeeSource = selectedOption.getAttribute('data-source');

            console.error('🚨 CRITICAL ERROR: Gaji pokok tidak valid!');
            console.error(`   Employee: ${employeeName}`);
            console.error(`   Source: ${employeeSource}`);
            console.error(`   Gaji attribute: "${gajiPokokAttr}"`);
            console.error(`   Parsed value: ${gajiPokok}`);

            // Clear fields
            if (gajiPerhariMentahDisplay) gajiPerhariMentahDisplay.value = '';
            if (gajiHariItuDisplay) gajiHariItuDisplay.value = '';
            if (gajiHariItuInput) gajiHariItuInput.value = '';

            // Show user-friendly error message
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gaji Pokok Belum Diatur',
                    html: `
                        <p><strong>${employeeName}</strong> belum memiliki gaji pokok yang valid.</p>
                        <hr>
                        <p class="text-start mb-2"><strong>Detail:</strong></p>
                        <ul class="text-start">
                            <li>Source: ${employeeSource}</li>
                            <li>Gaji: ${gajiPokokAttr || 'NULL/Kosong'}</li>
                        </ul>
                        <hr>
                        <p class="small text-muted">
                            <i class="bi bi-exclamation-triangle"></i>
                            Silakan hubungi admin untuk mengatur gaji pokok karyawan ini terlebih dahulu.
                        </p>
                    `,
                    confirmButtonText: 'OK, Saya Mengerti',
                    confirmButtonColor: '#dc3545'
                });
            } else {
                alert(`⚠️ GAJI POKOK BELUM DIATUR!\n\nKaryawan: ${employeeName}\nSource: ${employeeSource}\nGaji: ${gajiPokokAttr || 'NULL/Kosong'}\n\nSilakan hubungi admin untuk mengatur gaji pokok karyawan ini.`);
            }

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

    // Format functions already defined at the top of initializeForm()

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

            // ===== BUSINESS LOGIC VALIDATION =====
            // Cek apakah karyawan sudah absen di pembibitan yang sama hari ini
            const pembibitanId = document.getElementById('pembibitan_id').value;

            if (window.currentEmployeeRiwayat && pembibitanId) {
                const sudahAbsenDiPembibitanSama = window.currentEmployeeRiwayat.some(riwayat => {
                    return riwayat.pembibitan_id == pembibitanId;
                });

                if (sudahAbsenDiPembibitanSama) {
                    console.error('❌ BUSINESS LOGIC VIOLATION: Karyawan sudah absen di pembibitan yang sama');

                    if (typeof Swal !== 'undefined') {
                        await Swal.fire({
                            icon: 'error',
                            title: 'Tidak Bisa Absen!',
                            html: `
                                <p><strong>Karyawan ini sudah absen di pembibitan yang sama hari ini.</strong></p>
                                <hr>
                                <p class="text-start mb-2"><strong>Aturan Bisnis:</strong></p>
                                <ul class="text-start">
                                    <li><i class="bi bi-x-circle text-danger"></i> <strong>TIDAK BOLEH</strong> absen 2x di pembibitan yang sama</li>
                                    <li><i class="bi bi-check-circle text-success"></i> <strong>BOLEH</strong> absen di pembibitan yang berbeda</li>
                                </ul>
                                <hr>
                                <p class="small text-muted">Silakan pilih pembibitan yang berbeda atau batalkan absensi.</p>
                            `,
                            confirmButtonText: 'OK, Saya Mengerti',
                            confirmButtonColor: '#dc3545'
                        });
                    } else {
                        alert('❌ TIDAK BISA ABSEN!\n\nKaryawan ini sudah absen di pembibitan yang sama hari ini.\n\nATURAN BISNIS:\n✗ TIDAK BOLEH absen 2x di pembibitan yang SAMA\n✓ BOLEH absen di pembibitan yang BERBEDA\n\nSilakan pilih pembibitan yang berbeda.');
                    }

                    return false;
                }

                // Informative message: Allowed to add 2nd attendance at different pembibitan
                if (window.currentEmployeeRiwayat.length > 0 && pembibitanId) {
                    const pembibitanSelectOption = document.querySelector(`#pembibitan_id option[value="${pembibitanId}"]`);
                    const pembibitanName = pembibitanSelectOption ? pembibitanSelectOption.textContent : 'pembibitan berbeda';

                    console.log('✅ BUSINESS LOGIC OK: Absen kedua di pembibitan berbeda diperbolehkan');

                    if (typeof Swal !== 'undefined') {
                        const confirmResult = await Swal.fire({
                            icon: 'info',
                            title: 'Konfirmasi Absensi Kedua',
                            html: `
                                <p>Karyawan ini sudah absen <strong>${window.currentEmployeeRiwayat.length}x</strong> hari ini.</p>
                                <p>Akan menambah absensi di: <strong>${pembibitanName}</strong></p>
                                <hr>
                                <p class="small text-muted">
                                    <i class="bi bi-info-circle"></i>
                                    Ini diperbolehkan karena di pembibitan yang berbeda.
                                </p>
                            `,
                            showCancelButton: true,
                            confirmButtonText: '<i class="bi bi-check-circle"></i> Ya, Lanjutkan',
                            cancelButtonText: 'Batal',
                            confirmButtonColor: '#28a745'
                        });

                        if (!confirmResult.isConfirmed) {
                            console.log('User cancelled second attendance');
                            return false;
                        }
                    } else {
                        const confirmed = confirm(`Karyawan ini sudah absen ${window.currentEmployeeRiwayat.length}x hari ini.\n\nAkan menambah absensi di: ${pembibitanName}\n\nLanjutkan?`);
                        if (!confirmed) {
                            return false;
                        }
                    }
                }
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
