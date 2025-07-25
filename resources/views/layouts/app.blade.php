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

        /* SweetAlert2 Custom Styling */
        .swal2-popup {
            font-family: 'Poppins', sans-serif !important;
            border-radius: 1rem !important;
        }

        .swal2-title {
            font-weight: 600 !important;
            color: #1f2937 !important;
        }

        .swal2-confirm, .swal2-cancel {
            border-radius: 0.5rem !important;
            font-weight: 500 !important;
            padding: 0.75rem 1.5rem !important;
        }

        .swal2-confirm {
            background-color: #3b82f6 !important;
        }

        .swal2-confirm:hover {
            background-color: #2563eb !important;
        }

        .swal2-cancel {
            background-color: #6b7280 !important;
        }

        .swal2-cancel:hover {
            background-color: #4b5563 !important;
        }

        /* Field validation animations */
        .field-error {
            animation: shake 0.5s ease-in-out;
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-3px); }
            20%, 40%, 60%, 80% { transform: translateX(3px); }
        }
    </style>
</head>

<body class="font-sans antialiased">
    @include('layouts.navigation')

    <!-- Page Heading -->
    {{-- @isset($header)
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset --}}

    {{-- <!-- Page Content -->
    <main>
        {{ $slot }}
    </main> --}}

    <!-- Additional Scripts -->
    @stack('scripts')

    <!-- Flash Messages for SweetAlert2 -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set global message variables for SweetAlert2
            @if(session('success'))
                window.successMessage = @json(session('success'));
        @endif

            @if(session('error'))
                window.errorMessage = @json(session('error'));
        @endif

            @if(session('warning'))
                window.warningMessage = @json(session('warning'));
        @endif

            @if(session('info'))
                window.infoMessage = @json(session('info'));
        @endif

            // Show messages with SweetAlert2 after a short delay to ensure it's loaded
            setTimeout(function() {
                if (window.successMessage && window.showMessage) {
                    window.showMessage.success(window.successMessage);
                }
                if (window.errorMessage && window.showMessage) {
                    window.showMessage.error(window.errorMessage);
                }
                if (window.warningMessage && window.showMessage) {
                    window.showMessage.warning(window.warningMessage);
                }
                if (window.infoMessage && window.showMessage) {
                    window.showMessage.info(window.infoMessage);
                }
            }, 100);
        });
    </script>
</body>
</html>
