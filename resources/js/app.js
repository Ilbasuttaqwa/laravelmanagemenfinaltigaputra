// Modern Management System - Production Ready

// Import absensi bulk functionality
import './absensi-bulk.js';

// Modern JavaScript enhancements
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Modern Management System loaded');
    
    // Initialize modern utilities manually
    window.cacheManager = new (class CacheManager {
        constructor() {
            this.cache = new Map();
            this.cacheTimeout = 5 * 60 * 1000;
        }
        set(key, value, ttl = this.cacheTimeout) {
            const item = { value, timestamp: Date.now(), ttl };
            this.cache.set(key, item);
            localStorage.setItem(`cache_${key}`, JSON.stringify(item));
        }
        get(key) {
            if (this.cache.has(key)) {
                const item = this.cache.get(key);
                if (Date.now() - item.timestamp < item.ttl) {
                    return item.value;
                }
                this.cache.delete(key);
            }
            const stored = localStorage.getItem(`cache_${key}`);
            if (stored) {
                try {
                    const item = JSON.parse(stored);
                    if (Date.now() - item.timestamp < item.ttl) {
                        this.cache.set(key, item);
                        return item.value;
                    }
                    localStorage.removeItem(`cache_${key}`);
                } catch (e) {
                    localStorage.removeItem(`cache_${key}`);
                }
            }
            return null;
        }
        clearAll() {
            this.cache.clear();
            Object.keys(localStorage).forEach(key => {
                if (key.startsWith('cache_')) {
                    localStorage.removeItem(key);
                }
            });
        }
    })();
    
    window.formManager = new (class FormManager {
        async submitForm(form) {
            const formData = new FormData(form);
            const url = form.action;
            const method = form.method || 'POST';
            
            if (!formData.has('_token')) {
                const token = document.querySelector('meta[name="csrf-token"]')?.content;
                if (token) {
                    formData.append('_token', token);
                }
            }
            
            this.showLoading(form);
            
            try {
                const response = await fetch(url, {
                    method,
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const result = await response.json();
                this.hideLoading(form);
                return result;
            } catch (error) {
                this.hideLoading(form);
                throw error;
            }
        }
        
        showLoading(form) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Menyimpan...';
            }
        }
        
        hideLoading(form) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-save"></i> Simpan';
            }
        }
        
        showSuccess(message) {
            alert('Success: ' + message);
        }
        
        showError(message) {
            alert('Error: ' + message);
        }
    })();
    
    // Real-time functionality integrated
    console.log('✅ Real-time functionality ready');

    // Absensi Bulk Management
    window.absensiBulk = new (class AbsensiBulkManager {
        constructor() {
            this.allEmployees = [];
            this.allPembibitans = [];
        }

        // Load employees for bulk attendance
        loadBulkEmployees() {
            fetch('/manager/absensis/refresh-master-data', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.allEmployees = data.data.employees;
                    this.allPembibitans = data.data.pembibitans;
                    this.updateBulkEmployeeList(this.allEmployees, this.allPembibitans);
                    console.log('✅ Bulk employees loaded:', this.allEmployees.length);
                }
            })
            .catch(error => {
                console.error('Error loading bulk employees:', error);
            });
        }

        // Function to update bulk employee list
        updateBulkEmployeeList(employees, pembibitans = []) {
            const tbody = document.getElementById('employeesTableBodyBulk');
            if (!tbody) return;
            
            // Clear existing rows
            tbody.innerHTML = '';
            
            // Add new rows
            employees.forEach(employee => {
                const row = document.createElement('tr');
                row.setAttribute('data-employee-id', employee.id);
                
                // Build pembibitan options
                let pembibitanOptions = '<option value="">Pilih Pembibitan</option>';
                pembibitans.forEach(pembibitan => {
                    pembibitanOptions += '<option value="' + pembibitan.id + '" data-lokasi="' + (pembibitan.lokasi ? pembibitan.lokasi.nama_lokasi : '') + '" data-kandang="' + (pembibitan.kandang ? pembibitan.kandang.nama_kandang : '') + '">' + pembibitan.judul + '</option>';
                });
                
                row.innerHTML = 
                    '<td>' +
                        '<input type="checkbox" class="form-check-input employee-checkbox-bulk" ' +
                               'value="' + employee.id + '" ' +
                               'data-employee="' + JSON.stringify(employee).replace(/"/g, '&quot;') + '">' +
                    '</td>' +
                    '<td>' +
                        '<strong>' + employee.nama + '</strong>' +
                        '<br><small class="text-muted">' + (employee.jabatan === 'karyawan' ? 'karyawan kandang' : (employee.jabatan === 'karyawan_gudang' ? 'karyawan gudang' : employee.jabatan)) + '</small>' +
                    '</td>' +
                    '<td class="lokasi-cell" data-employee-id="' + employee.id + '">-</td>' +
                    '<td class="kandang-cell" data-employee-id="' + employee.id + '">-</td>' +
                    '<td>' +
                        '<span class="badge bg-info">Rp ' + new Intl.NumberFormat('id-ID').format(employee.gaji_pokok) + '</span>' +
                    '</td>' +
                    '<td>' +
                        '<select class="form-select form-select-sm status-select-bulk" data-employee-id="' + employee.id + '">' +
                            '<option value="full">Full Day</option>' +
                            '<option value="setengah_hari">½ Hari</option>' +
                        '</select>' +
                    '</td>' +
                    '<td>' +
                        '<select class="form-select form-select-sm pembibitan-select-bulk" data-employee-id="' + employee.id + '" onchange="updateLokasiKandang(this)">' +
                            pembibitanOptions +
                        '</select>' +
                    '</td>' +
                    '<td>' +
                        '<button type="button" class="btn btn-warning btn-sm" onclick="quickAbsenBulk(\'' + employee.id + '\')">' +
                            '<i class="bi bi-lightning-fill"></i>' +
                        '</button>' +
                    '</td>';
                tbody.appendChild(row);
            });
        }

        // Update lokasi and kandang when pembibitan is selected
        updateLokasiKandang(selectElement) {
            const employeeId = selectElement.getAttribute('data-employee-id');
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            
            const lokasiCell = document.querySelector('.lokasi-cell[data-employee-id="' + employeeId + '"]');
            const kandangCell = document.querySelector('.kandang-cell[data-employee-id="' + employeeId + '"]');
            
            if (selectedOption.value) {
                const lokasi = selectedOption.getAttribute('data-lokasi');
                const kandang = selectedOption.getAttribute('data-kandang');
                
                lokasiCell.textContent = lokasi || '-';
                kandangCell.textContent = kandang || '-';
            } else {
                lokasiCell.textContent = '-';
                kandangCell.textContent = '-';
            }
        }

        // Filter employees by pembibitan
        filterEmployeesBulk() {
            const pembibitanId = document.getElementById('filterPembibitanBulk').value;
            
            if (pembibitanId) {
                // Filter by pembibitan
                const filteredEmployees = this.allEmployees.filter(employee => {
                    // For now, show all employees regardless of pembibitan
                    // This can be enhanced later if needed
                    return true;
                });
                this.updateBulkEmployeeList(filteredEmployees, this.allPembibitans);
            } else {
                // Show all employees
                this.updateBulkEmployeeList(this.allEmployees, this.allPembibitans);
            }
        }

        // Toggle select all employees
        toggleSelectAll() {
            const selectAll = document.getElementById('selectAllBulk');
            const checkboxes = document.querySelectorAll('.employee-checkbox-bulk');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
        }

        // Submit bulk attendance
        async submitBulkAttendance() {
            const selectedEmployees = [];
            const tanggal = document.getElementById('tanggalBulk').value;
            
            if (!tanggal) {
                alert('Mohon pilih tanggal absensi');
                return;
            }

            document.querySelectorAll('.employee-checkbox-bulk:checked').forEach(checkbox => {
                const employeeId = checkbox.value;
                const statusSelect = document.querySelector('.status-select-bulk[data-employee-id="' + employeeId + '"]');
                const pembibitanSelect = document.querySelector('.pembibitan-select-bulk[data-employee-id="' + employeeId + '"]');
                
                selectedEmployees.push({
                    id: employeeId,
                    status: statusSelect.value,
                    pembibitan_id: pembibitanSelect.value || null
                });
            });

            if (selectedEmployees.length === 0) {
                alert('Mohon pilih minimal satu karyawan');
                return;
            }

            // Show loading
            const submitBtn = document.querySelector('button[onclick="window.absensiBulk.submitBulkAttendance()"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Menyimpan...';
            submitBtn.disabled = true;

            try {
                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                 document.querySelector('input[name="_token"]')?.value ||
                                 window.Laravel?.csrfToken;
                
                if (!csrfToken) {
                    alert('CSRF token tidak ditemukan. Silakan refresh halaman.');
                    return;
                }
                
                // Submit data
                const response = await fetch('/manager/absensis/bulk-store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        tanggal: tanggal,
                        employees: selectedEmployees
                    })
                });

                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status + ': ' + response.statusText);
                }
                
                const data = await response.json();
                console.log('Response data:', data);
                
                if (data.success) {
                    alert('Tambah cepat absensi berhasil! ' + data.success_count + ' berhasil, ' + data.error_count + ' gagal');
                    if (data.errors && data.errors.length > 0) {
                        console.log('Errors:', data.errors);
                    }
                    // Refresh table
                    if (typeof $('#absensiTable') !== 'undefined' && $('#absensiTable').DataTable) {
                        $('#absensiTable').DataTable().ajax.reload();
                    }
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('bulkAttendanceModal'));
                    if (modal) {
                        modal.hide();
                    }
                } else {
                    alert('Error: ' + (data.message || 'Gagal menyimpan data'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan data: ' + error.message);
            } finally {
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        }

        // Quick absen function
        quickAbsenBulk(employeeId) {
            const employee = document.querySelector('[data-employee-id="' + employeeId + '"]');
            const employeeData = JSON.parse(employee.querySelector('.employee-checkbox-bulk').getAttribute('data-employee'));
            
            // Auto-fill form
            document.getElementById('quickEmployeeIdBulk').value = employeeId;
            document.getElementById('quickEmployeeNameBulk').value = employeeData.nama;
            document.getElementById('quickTanggalBulk').value = document.getElementById('tanggalBulk').value;
            
            // Show quick absen modal (if exists)
            const quickModal = document.getElementById('quickAbsenModal');
            if (quickModal) {
                const modal = new bootstrap.Modal(quickModal);
                modal.show();
            }
        }

        // Show bulk attendance modal
        showBulkAttendance() {
            const modal = new bootstrap.Modal(document.getElementById('bulkAttendanceModal'));
            modal.show();
            
            // Load employees when modal opens
            this.loadBulkEmployees();
        }

        // Initialize
        init() {
            console.log('Absensi Bulk JavaScript loaded successfully');
            
            // Set default date to today
            const today = new Date().toISOString().split('T')[0];
            const tanggalInput = document.getElementById('tanggalBulk');
            if (tanggalInput && !tanggalInput.value) {
                tanggalInput.value = today;
            }
        }
    })();

    // Initialize absensi bulk
    window.absensiBulk.init();

    console.log('✅ Modern utilities initialized');
});

// Global functions for backward compatibility (outside DOMContentLoaded)
window.loadBulkEmployees = () => window.absensiBulk?.loadBulkEmployees();
window.updateBulkEmployeeList = (employees, pembibitans) => window.absensiBulk?.updateBulkEmployeeList(employees, pembibitans);
window.updateLokasiKandang = (selectElement) => window.absensiBulk?.updateLokasiKandang(selectElement);
window.filterEmployeesBulk = () => window.absensiBulk?.filterEmployeesBulk();
window.toggleSelectAll = () => window.absensiBulk?.toggleSelectAll();
window.submitBulkAttendance = () => window.absensiBulk?.submitBulkAttendance();
window.quickAbsenBulk = (employeeId) => window.absensiBulk?.quickAbsenBulk(employeeId);
window.showBulkAttendance = () => window.absensiBulk?.showBulkAttendance();
