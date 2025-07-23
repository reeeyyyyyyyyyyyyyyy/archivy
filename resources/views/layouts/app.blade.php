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
    </style>
</head>

<body class="font-sans antialiased">
    @include('layouts.navigation')

    <!-- Global Notification System -->
    <div id="notificationContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

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

            // Create notification element
            const notification = document.createElement('div');
            notification.className = `
                    notification flex items-center p-4 rounded-xl shadow-lg border transform transition-all duration-300 ease-in-out translate-x-full opacity-0
                    ${type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : ''}
                    ${type === 'error' ? 'bg-red-50 border-red-200 text-red-800' : ''}
                    ${type === 'warning' ? 'bg-yellow-50 border-yellow-200 text-yellow-800' : ''}
                    ${type === 'info' ? 'bg-blue-50 border-blue-200 text-blue-800' : ''}
                `;

            // Icon based on type
            const icons = {
                success: 'fas fa-check-circle text-green-500',
                error: 'fas fa-exclamation-circle text-red-500',
                warning: 'fas fa-exclamation-triangle text-yellow-500',
                info: 'fas fa-info-circle text-blue-500'
            };

            notification.innerHTML = `
                    <div class="flex items-center min-w-80">
                        <i class="${icons[type]} mr-3 text-lg"></i>
                        <div class="flex-1">
                            <p class="font-medium">${message}</p>
                        </div>
                        <button onclick="removeNotification(this.parentElement.parentElement)" 
                                class="ml-4 text-gray-400 hover:text-gray-600 transition-colors">
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

        function removeNotification(notification) {
            notification.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.parentElement.removeChild(notification);
                }
            }, 300);
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
    </script>
</body>

</html>
