// Absensi Bulk Management - ES6 Module version
console.log('Loading absensi-bulk.js...');

// Create absensiBulk object
const absensiBulk = {
        allEmployees: [],
        allPembibitans: [],
        
        // Show bulk attendance modal
        showBulkAttendance: function() {
            const modal = new bootstrap.Modal(document.getElementById('bulkAttendanceModal'));
            modal.show();
            this.loadBulkEmployees();
        },
        
        // Load employees for bulk attendance
        loadBulkEmployees: function() {
            const self = this;
            // Use dynamic base URL for cPanel compatibility
            const baseUrl = window.location.origin;
            fetch(baseUrl + '/manager/absensis/refresh-master-data', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    self.allEmployees = data.data.employees;
                    self.allPembibitans = data.data.pembibitans;
                    self.updateBulkEmployeeList(self.allEmployees, self.allPembibitans);
                    console.log('Bulk employees loaded:', self.allEmployees.length);
                }
            })
            .catch(error => {
                console.error('Error loading bulk employees:', error);
            });
        },

        // Function to generate color for pembibitan based on ID
        generatePembibitanColor: function(pembibitanId) {
            const colors = [
                'primary', 'success', 'info', 'warning', 'danger', 
                'secondary', 'dark', 'light', 'primary', 'success',
                'info', 'warning', 'danger', 'secondary', 'dark'
            ];
            return colors[pembibitanId % colors.length];
        },
        
        // Update bulk employee list
        updateBulkEmployeeList: function(employees, pembibitans) {
            const tbody = document.getElementById('employeesTableBodyBulk');
            if (!tbody) return;
            
            tbody.innerHTML = '';
            
            employees.forEach(function(employee) {
                const row = document.createElement('tr');
                row.setAttribute('data-employee-id', employee.id);
                
                // Build pembibitan options with colors
                let pembibitanOptions = '<option value="">Pilih Pembibitan</option>';
                const colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary', 'dark', 'light'];
                pembibitans.forEach(function(pembibitan, index) {
                    const lokasiName = pembibitan.lokasi ? pembibitan.lokasi.nama_lokasi : '';
                    const kandangName = pembibitan.kandang ? pembibitan.kandang.nama_kandang : '';
                    const colorClass = colors[index % colors.length];
                    pembibitanOptions += '<option value="' + pembibitan.id + '" data-lokasi="' + lokasiName + '" data-kandang="' + kandangName + '" data-color="' + colorClass + '">' + pembibitan.judul + '</option>';
                });
                
                const jabatanDisplay = employee.jabatan === 'karyawan' ? 'karyawan kandang' : (employee.jabatan === 'karyawan_gudang' ? 'karyawan gudang' : employee.jabatan);
                const gajiFormatted = new Intl.NumberFormat('id-ID').format(employee.gaji_pokok);
                const employeeDataJson = JSON.stringify(employee).replace(/"/g, '&quot;');
                
                row.innerHTML = 
                    '<td><input type="checkbox" class="form-check-input employee-checkbox-bulk" value="' + employee.id + '" data-employee="' + employeeDataJson + '"></td>' +
                    '<td><strong>' + employee.nama + '</strong><br><small class="text-muted">' + jabatanDisplay + '</small></td>' +
                    '<td class="lokasi-cell" data-employee-id="' + employee.id + '">-</td>' +
                    '<td class="kandang-cell" data-employee-id="' + employee.id + '">-</td>' +
                    '<td><span class="badge bg-info">Rp ' + gajiFormatted + '</span></td>' +
                    '<td><select class="form-select form-select-sm status-select-bulk" data-employee-id="' + employee.id + '"><option value="full">Full Day</option><option value="setengah_hari">½ Hari</option></select></td>' +
                    '<td><select class="form-select form-select-sm pembibitan-select-bulk" data-employee-id="' + employee.id + '" onchange="updateLokasiKandang(this)">' + pembibitanOptions + '</select></td>' +
                    '<td><button type="button" class="btn btn-warning btn-sm" onclick="quickAbsenBulk(\'' + employee.id + '\')"><i class="bi bi-lightning-fill"></i></button></td>';
                
                tbody.appendChild(row);
            });
        },
        
        // Update lokasi and kandang when pembibitan is selected
        updateLokasiKandang: function(selectElement) {
            console.log('updateLokasiKandang called');
            const employeeId = selectElement.getAttribute('data-employee-id');
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            
            console.log('Employee ID:', employeeId);
            console.log('Selected option:', selectedOption.value, selectedOption.textContent);
            
            const lokasiCell = document.querySelector('.lokasi-cell[data-employee-id="' + employeeId + '"]');
            const kandangCell = document.querySelector('.kandang-cell[data-employee-id="' + employeeId + '"]');
            
            if (selectedOption.value) {
                const lokasi = selectedOption.getAttribute('data-lokasi');
                const kandang = selectedOption.getAttribute('data-kandang');
                const colorClass = selectedOption.getAttribute('data-color');
                const pembibitanTitle = selectedOption.textContent;
                
                console.log('Color class:', colorClass);
                console.log('Pembibitan title:', pembibitanTitle);
                
                // Update lokasi and kandang
                lokasiCell.textContent = lokasi || '-';
                kandangCell.textContent = kandang || '-';
                
                // Update pembibitan display with color
                const pembibitanCell = selectElement.parentElement;
                pembibitanCell.innerHTML = '<span class="badge bg-' + colorClass + '">' + pembibitanTitle + '</span>';
                console.log('Pembibitan cell updated with color');
            } else {
                lokasiCell.textContent = '-';
                kandangCell.textContent = '-';
                
                // Reset pembibitan display
                const pembibitanCell = selectElement.parentElement;
                pembibitanCell.innerHTML = '<select class="form-select form-select-sm pembibitan-select-bulk" data-employee-id="' + employeeId + '" onchange="updateLokasiKandang(this)">' + 
                    Array.from(selectElement.options).map(option => 
                        '<option value="' + option.value + '" data-lokasi="' + option.getAttribute('data-lokasi') + '" data-kandang="' + option.getAttribute('data-kandang') + '" data-color="' + option.getAttribute('data-color') + '">' + option.textContent + '</option>'
                    ).join('') + 
                '</select>';
            }
        },
        
        
        // Toggle select all employees
        toggleSelectAll: function() {
            const selectAll = document.getElementById('selectAllBulk');
            const checkboxes = document.querySelectorAll('.employee-checkbox-bulk');
            
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = selectAll.checked;
            });
        },
        
        // Submit bulk attendance
        submitBulkAttendance: async function() {
            const selectedEmployees = [];
            const tanggal = document.getElementById('tanggalBulk').value;
            
            if (!tanggal) {
                alert('Mohon pilih tanggal absensi');
                return;
            }
            
            document.querySelectorAll('.employee-checkbox-bulk:checked').forEach(function(checkbox) {
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
            
            const submitBtn = document.querySelector('button[onclick="submitBulkAttendance()"]');
            if (!submitBtn) return;
            
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Menyimpan...';
            submitBtn.disabled = true;
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                const baseUrl = window.location.origin;
                const response = await fetch(baseUrl + '/manager/absensis/bulk-store', {
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
                
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status + ': ' + response.statusText);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Tambah cepat absensi berhasil! ' + data.success_count + ' berhasil, ' + data.error_count + ' gagal');
                    if (typeof $('#absensiTable') !== 'undefined' && $('#absensiTable').DataTable) {
                        $('#absensiTable').DataTable().ajax.reload();
                    }
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
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        },
        
        // Quick absen function
        quickAbsenBulk: function(employeeId) {
            console.log('Quick absen for:', employeeId);
        }
    };
    
    // Expose global functions
    window.showBulkAttendance = function() {
        window.absensiBulk.showBulkAttendance();
    };
    
    window.updateLokasiKandang = function(selectElement) {
        window.absensiBulk.updateLokasiKandang(selectElement);
    };
    
    
    window.toggleSelectAll = function() {
        window.absensiBulk.toggleSelectAll();
    };
    
    window.submitBulkAttendance = function() {
        window.absensiBulk.submitBulkAttendance();
    };
    
    window.quickAbsenBulk = function(employeeId) {
        window.absensiBulk.quickAbsenBulk(employeeId);
    };
    
    // Set default date
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        const tanggalInput = document.getElementById('tanggalBulk');
        if (tanggalInput && !tanggalInput.value) {
            tanggalInput.value = today;
        }
    });
    
    console.log('absensi-bulk.js loaded successfully');

// Export for ES6 modules
export default absensiBulk;

