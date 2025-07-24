<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ARSIPIN - Sistem Arsip Pintar DPMPTSP Provinsi Jawa Timur</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Additional Styles -->
    @stack('styles')

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Prevent layout shifting during Alpine.js initialization */
        [x-cloak] {
            display: none !important;
        }

        /* Ensure consistent sidebar behavior */
        .sidebar-stable {
            will-change: transform;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
        }

        /* Smooth transitions for all navigation elements */
        .nav-transition {
            transition: all 0.2s ease-out;
        }

        /* Prevent content jump during page loads */
        .main-content {
            min-height: 100vh;
            transition: none;
        }

        /* Fix for submenu animation stability */
        .submenu-container {
            overflow: hidden;
            transform-origin: top;
        }

        /* Warning Animation for Empty Fields */
        .field-warning {
            animation: fieldWarning 0.6s ease-in-out;
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
        }

        @keyframes fieldWarning {
            0% {
                transform: translateX(0);
                border-color: #d1d5db;
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
            25% {
                transform: translateX(-5px);
                border-color: #ef4444;
                box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.3);
            }
            50% {
                transform: translateX(5px);
                border-color: #ef4444;
                box-shadow: 0 0 0 6px rgba(239, 68, 68, 0.2);
            }
            75% {
                transform: translateX(-3px);
                border-color: #ef4444;
                box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
            }
            100% {
                transform: translateX(0);
                border-color: #ef4444;
                box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
            }
        }

        /* Pulse Animation for Required Fields */
        .field-required {
            position: relative;
        }

        .field-required::after {
            content: '*';
            color: #ef4444;
            position: absolute;
            top: -5px;
            right: -10px;
            font-size: 16px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.5;
                transform: scale(1.2);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Shake Animation for Invalid Fields */
        .field-invalid {
            animation: shake 0.5s ease-in-out;
            border-color: #ef4444 !important;
            background-color: #fef2f2 !important;
        }

        @keyframes shake {
            0%, 100% {
                transform: translateX(0);
            }
            10%, 30%, 50%, 70%, 90% {
                transform: translateX(-5px);
            }
            20%, 40%, 60%, 80% {
                transform: translateX(5px);
            }
        }

        /* Bounce Animation for Success Fields */
        .field-success {
            animation: bounce 0.6s ease-in-out;
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1) !important;
        }

        @keyframes bounce {
            0%, 20%, 53%, 80%, 100% {
                transform: translate3d(0, 0, 0);
            }
            40%, 43% {
                transform: translate3d(0, -8px, 0);
            }
            70% {
                transform: translate3d(0, -4px, 0);
            }
            90% {
                transform: translate3d(0, -2px, 0);
            }
        }

        /* Floating Label Animation */
        .floating-label {
            position: relative;
        }

        .floating-label input:focus + label,
        .floating-label input:not(:placeholder-shown) + label {
            transform: translateY(-25px) scale(0.85);
            color: #3b82f6;
        }

        .floating-label label {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            transition: all 0.3s ease;
            pointer-events: none;
            background: white;
            padding: 0 4px;
        }

        /* Loading Spinner */
        .loading-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        /* Fade In Animation */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Slide In Animation */
        .slide-in {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>

<body class="font-sans antialiased">
    @include('layouts.navigation')

    <!-- Global Notification System -->
    <div id="notificationContainer" class="fixed top-0 right-0 z-50 w-full h-full flex flex-col items-end justify-start pointer-events-none">
        <!-- Notification items will be injected here -->
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
            <div class="p-6">
                <!-- Header -->
                <div class="flex items-center justify-center mb-4">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                </div>

                <!-- Content -->
                <div class="text-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Konfirmasi Penghapusan</h3>
                    <p id="deleteModalMessage" class="text-gray-600">Apakah Anda yakin ingin menghapus item ini?</p>
                    <p class="text-sm text-red-600 mt-2">Tindakan ini tidak dapat dibatalkan!</p>
                </div>

                <!-- Actions -->
                <div class="flex space-x-3">
                    <button onclick="closeDeleteModal()"
                        class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button id="confirmDeleteBtn"
                        class="flex-1 px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                        <i class="fas fa-trash mr-2"></i>Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Action Modal (for status changes, etc.) -->
    <div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
            <div class="p-6">
                <!-- Header -->
                <div class="flex items-center justify-center mb-4">
                    <div id="confirmModalIcon" class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-question-circle text-blue-600 text-2xl"></i>
                    </div>
                </div>

                <!-- Content -->
                <div class="text-center mb-6">
                    <h3 id="confirmModalTitle" class="text-lg font-semibold text-gray-900 mb-2">Konfirmasi</h3>
                    <p id="confirmModalMessage" class="text-gray-600">Apakah Anda yakin?</p>
                </div>

                <!-- Actions -->
                <div class="flex space-x-3">
                    <button onclick="closeConfirmModal()"
                        class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button id="confirmActionBtn"
                        class="flex-1 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        <i class="fas fa-check mr-2"></i>Konfirmasi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Scripts -->
    @stack('scripts')

    <!-- Notification System Script -->
    <script>
        function showNotification(message, type = 'success', duration = 5000) {
            const container = document.getElementById('notificationContainer');

            // Create overlay for blur effect (only if not already present)
            let overlay = document.getElementById('notificationOverlay');
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.id = 'notificationOverlay';
                overlay.className = 'fixed inset-0 z-40 backdrop-blur-sm bg-black/10 pointer-events-none';
                document.body.appendChild(overlay);
                setTimeout(() => {
                    if (overlay) overlay.style.opacity = '1';
                }, 10);
            }

            // Create notification element
            const notification = document.createElement('div');
            notification.className = `
                notification pointer-events-auto flex items-center px-8 py-6 mb-4 mr-8 mt-8 rounded-3xl shadow-2xl border-4 max-w-md w-full bg-white/90 backdrop-blur-lg animate-fadeInScaleUp
                ${type === 'success' ? 'border-green-400' : ''}
                ${type === 'error' ? 'border-red-400' : ''}
                ${type === 'warning' ? 'border-yellow-400' : ''}
                ${type === 'info' ? 'border-blue-400' : ''}
            `;

            // Icon based on type
            const icons = {
                success: 'fas fa-check-circle text-green-500',
                error: 'fas fa-exclamation-circle text-red-500',
                warning: 'fas fa-exclamation-triangle text-yellow-500',
                info: 'fas fa-info-circle text-blue-500'
            };

            notification.innerHTML = `
                <div class="flex items-center w-full">
                    <i class="${icons[type]} mr-4 text-2xl"></i>
                    <div class="flex-1">
                        <p class="font-bold text-lg mb-1 ${type === 'success' ? 'text-green-700' : type === 'error' ? 'text-red-700' : type === 'warning' ? 'text-yellow-700' : 'text-blue-700'}">
                            ${type === 'success' ? 'Berhasil!' : type === 'error' ? 'Gagal!' : type === 'warning' ? 'Peringatan!' : 'Info'}
                        </p>
                        <div class="text-base text-gray-700">${message}</div>
                    </div>
                    <button onclick="removeNotification(this.parentElement.parentElement)" class="ml-6 text-gray-400 hover:text-gray-600 transition-colors text-2xl focus:outline-none" aria-label="Tutup notifikasi">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            container.appendChild(notification);

            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full', 'opacity-0');
            }, 100);

            // Auto remove after duration
            if (duration > 0) {
                setTimeout(() => {
                    removeNotification(notification);
                }, duration);
            }
        }
        window.showNotification = showNotification;

        function removeNotification(notification) {
            notification.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.parentElement.removeChild(notification);
                }
                // Remove overlay if no more notifications
                const container = document.getElementById('notificationContainer');
                if (container && container.children.length === 0) {
                    const overlay = document.getElementById('notificationOverlay');
                    if (overlay) overlay.remove();
                }
            }, 300);
        }

        // Field Validation and Animation Functions
        function showFieldWarning(fieldId, message = 'Field ini wajib diisi') {
            const field = document.getElementById(fieldId);
            if (field) {
                field.classList.add('field-warning');

                // Show warning message
                let warningDiv = field.parentNode.querySelector('.field-warning-message');
                if (!warningDiv) {
                    warningDiv = document.createElement('div');
                    warningDiv.className = 'field-warning-message text-red-600 text-sm mt-1 slide-in';
                    field.parentNode.appendChild(warningDiv);
                }
                warningDiv.textContent = message;

                // Remove warning after 3 seconds
                setTimeout(() => {
                    field.classList.remove('field-warning');
                    if (warningDiv) {
                        warningDiv.remove();
                    }
                }, 3000);
            }
        }

        function showFieldSuccess(fieldId) {
            const field = document.getElementById(fieldId);
            if (field) {
                field.classList.add('field-success');
                setTimeout(() => {
                    field.classList.remove('field-success');
                }, 2000);
            }
        }

        function showFieldInvalid(fieldId, message = 'Data tidak valid') {
            const field = document.getElementById(fieldId);
            if (field) {
                field.classList.add('field-invalid');

                // Show error message
                let errorDiv = field.parentNode.querySelector('.field-error-message');
                if (!errorDiv) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'field-error-message text-red-600 text-sm mt-1 slide-in';
                    field.parentNode.appendChild(errorDiv);
                }
                errorDiv.textContent = message;

                // Remove invalid after 3 seconds
                setTimeout(() => {
                    field.classList.remove('field-invalid');
                    if (errorDiv) {
                        errorDiv.remove();
                    }
                }, 3000);
            }
        }

        // Form validation function
        function validateForm(formId, requiredFields = []) {
            let isValid = true;
            const form = document.getElementById(formId);

            if (!form) return false;

            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field && !field.value.trim()) {
                    showFieldWarning(fieldId);
                    isValid = false;
                }
            });

            return isValid;
        }

        // Check for Laravel session messages
        @if (session('success'))
            showNotification("{{ session('success') }}", 'success');
        @endif

        @if (session('error'))
            showNotification("{{ session('error') }}", 'error');
        @endif

        @if (session('warning'))
            showNotification("{{ session('warning') }}", 'warning');
        @endif

        @if (session('info'))
            showNotification("{{ session('info') }}", 'info');
        @endif

        // Delete Modal Functions
        let deleteCallback = null;

        function showDeleteModal(message, onConfirm) {
            document.getElementById('deleteModalMessage').textContent = message;
            document.getElementById('deleteModal').classList.remove('hidden');
            deleteCallback = onConfirm;

            // Focus on modal for better UX
            document.getElementById('confirmDeleteBtn').focus();
        }
        window.showDeleteModal = showDeleteModal;

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            deleteCallback = null;
        }

        // Confirm delete action
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (deleteCallback) {
                deleteCallback();
            }
            closeDeleteModal();
        });

        // Confirm Action Modal Functions (for status changes, etc.)
        let confirmCallback = null;

        function showConfirmModal(title, message, buttonText = 'Konfirmasi', buttonClass = 'bg-blue-600 hover:bg-blue-700', onConfirm) {
            document.getElementById('confirmModalTitle').textContent = title;
            document.getElementById('confirmModalMessage').textContent = message;

            const confirmBtn = document.getElementById('confirmActionBtn');
            confirmBtn.innerHTML = `<i class="fas fa-check mr-2"></i>${buttonText}`;
            confirmBtn.className = `flex-1 px-4 py-3 ${buttonClass} text-white rounded-lg font-medium transition-colors`;

            document.getElementById('confirmModal').classList.remove('hidden');
            confirmCallback = onConfirm;

            // Focus on modal for better UX
            confirmBtn.focus();
        }
        window.showConfirmModal = showConfirmModal;

        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.add('hidden');
            confirmCallback = null;
        }

        // Confirm action
        document.getElementById('confirmActionBtn').addEventListener('click', function() {
            if (confirmCallback) {
                confirmCallback();
            }
            closeConfirmModal();
        });

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (!document.getElementById('deleteModal').classList.contains('hidden')) {
                    closeDeleteModal();
                }
                if (!document.getElementById('confirmModal').classList.contains('hidden')) {
                    closeConfirmModal();
                }
            }
        });

        @stack('scripts')
    </script>
    <script>
        function showNotification(message, type = 'success') {
            // Remove existing notification if any
            const oldNotif = document.getElementById('successNotification');
            if (oldNotif) oldNotif.remove();

            // Create overlay
            const overlay = document.createElement('div');
            overlay.id = 'successNotification';
            overlay.className = 'fixed inset-0 flex items-center justify-center z-50 pointer-events-auto backdrop-blur-sm bg-black/30';
            overlay.innerHTML = `
                <div class="bg-white/90 shadow-2xl rounded-3xl px-16 py-12 flex flex-col items-center animate-fadeInScaleUp border-4 border-green-400 max-w-lg w-full relative">
                    <div class="bg-green-100 rounded-full p-6 mb-5 animate-pop">
                        <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-green-700 mb-2">Berhasil!</div>
                    <div class="text-lg text-gray-700 text-center">${message}</div>
                </div>
            `;
            document.body.appendChild(overlay);
            setTimeout(() => {
                overlay.style.display = 'none';
            }, 2500);
        }
    </script>
    @if (session('success'))
        <div id="successNotification" class="fixed inset-0 flex items-center justify-center z-50 pointer-events-auto backdrop-blur-sm bg-black/30">
            <div class="bg-white/90 shadow-2xl rounded-3xl px-16 py-12 flex flex-col items-center animate-fadeInScaleUp border-4 border-green-400 max-w-lg w-full relative">
                <div class="bg-green-100 rounded-full p-6 mb-5 animate-pop">
                    <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="text-2xl font-bold text-green-700 mb-2">Berhasil!</div>
                <div class="text-lg text-gray-700 text-center">{{ session('success') }}</div>
            </div>
        </div>
        <script>
            setTimeout(() => {
                const notif = document.getElementById('successNotification');
                if (notif) notif.style.display = 'none';
            }, 2500);
        </script>
    @endif
    <style>
        @keyframes fadeInScaleUp {
            0% { opacity: 0; transform: scale(0.8) translateY(40px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-fadeInScaleUp { animation: fadeInScaleUp 0.5s cubic-bezier(.4,2,.6,1) both; }
        @keyframes pop {
            0% { transform: scale(0.7); }
            80% { transform: scale(1.15); }
            100% { transform: scale(1); }
        }
        .animate-pop { animation: pop 0.4s cubic-bezier(.4,2,.6,1) both; }
    </style>
</body>

</html>
