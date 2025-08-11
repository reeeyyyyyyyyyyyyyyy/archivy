import './bootstrap';

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
// import $ from 'jquery';

// window.$ = $;
// window.jQuery = $; // Commented out because jQuery is not imported

window.Alpine = Alpine;
Alpine.start();

// SweetAlert2 Global Configuration
window.Swal = Swal;

// 1. SUCCESS MESSAGES (Create, Update, Delete Success)
window.showMessage = {
    success: (message, duration = 4000) => {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: message,
            timer: duration,
            showConfirmButton: false,
            timerProgressBar: true,
            position: 'center',
            backdrop: true,
            allowOutsideClick: true
        });
    },

    // 2. ERROR MESSAGES
    error: (message, duration = 5000) => {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: message,
            timer: duration,
            showConfirmButton: false,
            timerProgressBar: true,
            position: 'center',
            backdrop: true,
            allowOutsideClick: true
        });
    },

    warning: (message, duration = 4000) => {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan!',
            text: message,
            timer: duration,
            showConfirmButton: false,
            timerProgressBar: true,
            position: 'center',
            backdrop: true,
            allowOutsideClick: true
        });
    },

    info: (message, duration = 4000) => {
        Swal.fire({
            icon: 'info',
            title: 'Informasi',
            text: message,
            timer: duration,
            showConfirmButton: false,
            timerProgressBar: true,
            position: 'center',
            backdrop: true,
            allowOutsideClick: true
        });
    }
};

// 3. CONFIRM CREATE - Show confirmation before creating
window.showCreateConfirm = (message = 'Apakah Anda yakin ingin menyimpan data ini?') => {
    return Swal.fire({
        icon: 'question',
        title: 'Konfirmasi Simpan',
        text: message,
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-save mr-2"></i>Ya, Simpan!',
        cancelButtonText: '<i class="fas fa-times mr-2"></i>Batal',
        reverseButtons: true,
        focusConfirm: true
    });
};

// 4. CONFIRM UPDATE - Show confirmation before updating
window.showUpdateConfirm = (message = 'Apakah Anda yakin ingin mengubah data ini?') => {
    return Swal.fire({
        icon: 'question',
        title: 'Konfirmasi Update',
        text: message,
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-edit mr-2"></i>Ya, Update!',
        cancelButtonText: '<i class="fas fa-times mr-2"></i>Batal',
        reverseButtons: true,
        focusConfirm: true
    });
};

// 5. CONFIRM DELETE - Show confirmation before deleting
window.showDeleteConfirm = (message = 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan!') => {
    return Swal.fire({
        icon: 'warning',
        title: 'Konfirmasi Hapus',
        text: message,
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-trash mr-2"></i>Ya, Hapus!',
        cancelButtonText: '<i class="fas fa-times mr-2"></i>Batal',
        reverseButtons: true,
        focusCancel: true
    });
};

// 6. FIELD VALIDATION - Show error for empty required fields
window.showValidationError = (message = 'Mohon lengkapi semua field yang wajib diisi!') => {
    Swal.fire({
        icon: 'error',
        title: 'Validasi Gagal',
        text: message,
        confirmButtonColor: '#ef4444',
        confirmButtonText: '<i class="fas fa-exclamation-triangle mr-2"></i>Mengerti',
        timer: 5000,
        timerProgressBar: true
    });
};

// Additional utility functions for specific use cases
window.showConfirm = (title, message, confirmText = 'Ya', icon = 'question') => {
    return Swal.fire({
        icon: icon,
        title: title,
        text: message,
        showCancelButton: true,
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#6b7280',
        confirmButtonText: confirmText,
        cancelButtonText: 'Batal',
        reverseButtons: true
    });
};

window.showAlert = (title, message, icon = 'info') => {
    return Swal.fire({
        icon: icon,
        title: title,
        text: message,
        confirmButtonColor: '#3b82f6',
        confirmButtonText: 'OK'
    });
};

// Field validation helper function
window.validateRequiredFields = (fieldIds) => {
    const emptyFields = [];
    const fieldsToAnimate = [];

    fieldIds.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            const value = field.value ? field.value.trim() : '';
            if (!value) {
                emptyFields.push(field.name || fieldId);
                fieldsToAnimate.push(field);
            }
        }
    });

    if (emptyFields.length > 0) {
        // Animate empty fields
        fieldsToAnimate.forEach(field => {
            field.classList.add('field-error');
            setTimeout(() => {
                field.classList.remove('field-error');
            }, 1000);
        });

        // Show validation error
        const fieldNames = emptyFields.join(', ');
        window.showValidationError(`Field berikut harus diisi: ${fieldNames}`);
        return false;
    }

    return true;
};

// Compatibility functions for existing code
window.showNotification = window.showMessage.success;
window.showConfirmModal = window.showConfirm;
window.showDeleteModal = window.showDeleteConfirm;
