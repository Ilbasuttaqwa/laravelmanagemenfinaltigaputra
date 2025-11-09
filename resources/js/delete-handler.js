/**
 * Universal Delete Handler with SweetAlert2
 * Handles delete confirmations for all master data
 */

// Check if SweetAlert2 is loaded
const hasSwal = typeof Swal !== 'undefined';

/**
 * Show delete confirmation dialog
 * @param {string} url - Delete URL
 * @param {string} itemName - Name of item to delete
 * @param {string} itemType - Type of item (e.g., 'lokasi', 'karyawan')
 * @returns {Promise}
 */
window.confirmDelete = async function(button) {
    // Get data from button attributes
    const itemId = button.getAttribute('data-id') ||
                   button.getAttribute('data-lokasi-id') ||
                   button.getAttribute('data-employee-id') ||
                   button.getAttribute('data-pembibitan-id') ||
                   button.getAttribute('data-kandang-id') ||
                   button.getAttribute('data-gudang-id') ||
                   button.getAttribute('data-mandor-id');

    const itemName = button.getAttribute('data-name') ||
                     button.getAttribute('data-lokasi-name') ||
                     button.getAttribute('data-employee-name') ||
                     button.getAttribute('data-pembibitan-name') ||
                     button.getAttribute('data-kandang-name') ||
                     button.getAttribute('data-gudang-name') ||
                     button.getAttribute('data-mandor-name') ||
                     'item ini';

    const itemType = button.getAttribute('data-type') || 'data';
    const deleteUrl = button.getAttribute('data-url');

    console.log('🗑️ Delete requested:', {
        itemId,
        itemName,
        itemType,
        deleteUrl
    });

    // Use SweetAlert2 if available, otherwise use standard confirm
    if (hasSwal) {
        const result = await Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `Apakah Anda yakin ingin menghapus <strong>${itemName}</strong>?<br><small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-trash me-2"></i>Ya, Hapus',
            cancelButtonText: '<i class="bi bi-x-circle me-2"></i>Batal',
            reverseButtons: true,
            focusCancel: true
        });

        if (result.isConfirmed) {
            await deleteItem(deleteUrl, itemName);
        }
    } else {
        // Fallback to standard confirm and Bootstrap modal
        const confirmed = confirm(`Apakah Anda yakin ingin menghapus ${itemName}?`);
        if (confirmed) {
            await deleteItem(deleteUrl, itemName);
        }
    }
};

/**
 * Perform delete action
 * @param {string} url - Delete URL
 * @param {string} itemName - Name of item
 */
async function deleteItem(url, itemName) {
    try {
        // Show loading
        if (hasSwal) {
            Swal.fire({
                title: 'Menghapus...',
                html: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) {
            throw new Error('CSRF token tidak ditemukan');
        }

        // Perform delete
        const response = await fetch(url, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        console.log('Delete response status:', response.status);

        if (!response.ok) {
            const errorText = await response.text();
            console.error('Delete failed:', errorText);
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();
        console.log('Delete response data:', data);

        // Show success message
        if (hasSwal) {
            await Swal.fire({
                title: 'Berhasil!',
                html: `<strong>${itemName}</strong> berhasil dihapus`,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            alert(`${itemName} berhasil dihapus`);
        }

        // Reload page or table
        setTimeout(() => {
            // Try to reload DataTable if exists
            if (typeof $ !== 'undefined' && $.fn.DataTable) {
                const table = $('table').DataTable();
                if (table) {
                    table.ajax.reload();
                    return;
                }
            }
            // Otherwise reload page
            window.location.reload();
        }, 500);

    } catch (error) {
        console.error('Delete error:', error);

        // Show error message
        if (hasSwal) {
            Swal.fire({
                title: 'Gagal Menghapus',
                html: `Terjadi kesalahan: ${error.message}<br><small class="text-muted">Silakan coba lagi atau hubungi administrator</small>`,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        } else {
            alert(`Gagal menghapus: ${error.message}`);
        }
    }
}

/**
 * Show success notification
 * @param {string} message
 */
window.showSuccess = function(message) {
    if (hasSwal) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: message,
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    } else if (typeof toastr !== 'undefined') {
        toastr.success(message);
    } else {
        alert(message);
    }
};

/**
 * Show error notification
 * @param {string} message
 */
window.showError = function(message) {
    if (hasSwal) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: message,
            confirmButtonText: 'OK'
        });
    } else if (typeof toastr !== 'undefined') {
        toastr.error(message);
    } else {
        alert('Error: ' + message);
    }
};

/**
 * Show warning notification
 * @param {string} message
 */
window.showWarning = function(message) {
    if (hasSwal) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian!',
            text: message,
            confirmButtonText: 'OK'
        });
    } else if (typeof toastr !== 'undefined') {
        toastr.warning(message);
    } else {
        alert('Warning: ' + message);
    }
};

console.log('✅ Delete handler and notifications loaded');
