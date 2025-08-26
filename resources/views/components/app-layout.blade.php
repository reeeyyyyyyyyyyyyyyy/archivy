{{-- resources/views/components/app-layout.blade.php --}}
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
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        .swal2-popup {
            font-family: 'Poppins', sans-serif;
            border-radius: 1rem;
        }

        .swal2-title {
            font-weight: 600;
        }

        .swal2-confirm, .swal2-cancel {
            border-radius: 0.5rem;
            font-weight: 500;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        @include('layouts.navigation')

        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main>
            {{ $slot }}
        </main>
    </div>

    <!-- Flash Messages for SweetAlert2 -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Hanya set message untuk non-login messages dan non-archive-related
            @if(session('success') && !str_contains(session('success'), 'Selamat datang kembali') && !session('show_add_related_button'))
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

        // Legacy support for existing code
        function showSuccessMessage(message) {
            if (window.showMessage) {
                window.showMessage.success(message);
            }
        }

        function showErrorMessage(message) {
            if (window.showMessage) {
                window.showMessage.error(message);
            }
        }

        function showWarningMessage(message) {
            if (window.showMessage) {
                window.showMessage.warning(message);
            }
        }

        function showInfoMessage(message) {
            if (window.showMessage) {
                window.showMessage.info(message);
            }
        }
    </script>

    @stack('scripts')
</body>
</html>
