<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-blue-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-tags text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Generate Label Box</h1>
                            <p class="text-gray-600">Fitur untuk menghasilkan label box dalam format PDF sesuai standar DINAS PENANAMAN MODAL DAN PTSP</p>
                        </div>
                    </div>
                    <a href="{{ route('intern.storage.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Total Rack</p>
                            <p class="text-3xl font-bold text-gray-900">{{ number_format(\App\Models\StorageRack::count()) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-warehouse text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Total Box</p>
                            <p class="text-3xl font-bold text-green-600">{{ number_format(\App\Models\StorageBox::count()) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-box text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Box Berisi Arsip</p>
                            <p class="text-3xl font-bold text-orange-600">{{ number_format(\App\Models\StorageBox::where('archive_count', '>', 0)->count()) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-archive text-orange-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Generation Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-cogs mr-2 text-blue-500"></i>Pengaturan Label
                </h3>

                <form method="POST" action="{{ route('intern.storage.generate-box-labels.process') }}" class="space-y-6" id="generateForm">
                    @csrf

                    <!-- Rack Selection -->
                    <div>
                        <label for="rack_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-warehouse mr-2 text-indigo-500"></i>Pilih RAK
                        </label>
                        <select name="rack_id" id="rack_id" class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4 text-base h-12">
                            <option value="">Pilih RAK</option>
                            @foreach(\App\Models\StorageRack::orderBy('id')->get() as $rack)
                                @php
                                    $boxCount = \App\Models\StorageBox::where('rack_id', $rack->id)->count();
                                    $filledBoxCount = \App\Models\StorageBox::where('rack_id', $rack->id)->where('archive_count', '>', 0)->count();
                                @endphp
                                <option value="{{ $rack->id }}">RAK {{ $rack->id }} - {{ $rack->name }} ({{ $filledBoxCount }}/{{ $boxCount }} box)</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Pilih RAK untuk generate label semua box dalam RAK tersebut</p>
                    </div>

                    <!-- Format Selection -->
                    <div>
                        <label for="format" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-file mr-2 text-green-500"></i>Format Output
                        </label>
                        <select name="format" id="format" class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                            <option value="pdf" selected>PDF</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Format output untuk label box</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-4">
                        <button type="button" id="previewBtn" class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-eye mr-2" id="eyeIcon"></i>
                            <span id="previewText">Preview Format</span>
                        </button>
                        <button type="submit" id="generateBtn" class="bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-download mr-2"></i>Generate & Download
                        </button>
                    </div>
                </form>
            </div>

            <!-- Preview Section -->
            <div id="previewSection" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6 hidden">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-eye mr-2 text-blue-500"></i>Preview Format Label
                </h3>
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-8 rounded-xl border border-blue-200">
                    <div class="max-w-md mx-auto">
                        <div class="label-preview">
                            <div class="label">
                                <div class="header">
                                    <p class="header-text">DINAS PENANAMAN MODAL DAN PTSP</p>
                                    <p class="header-text">PROVINSI JAWA TIMUR</p>
                                </div>
                                <div class="content">
                                    <div class="file-numbers">
                                        <div class="file-range">
                                            <p class="content-text">NOMOR BERKAS</p>
                                        </div>
                                        <div class="file-range">
                                            <p class="content-text">NO.ARSIP 1-8</p>
                                        </div>
                                        <div class="file-range">
                                            <p class="content-text">NO.ARSIP 9-16</p>
                                        </div>
                                    </div>
                                    <div class="box-number">
                                        <p class="content-text">NO. BOKS</p>
                                        <p class="content-text">1</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading Overlay -->
            <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white p-8 rounded-xl shadow-2xl text-center transform scale-100 transition-all duration-300">
                    <div class="animate-spin rounded-full h-16 w-16 border-4 border-blue-600 border-t-transparent mx-auto mb-6"></div>
                    <p class="text-xl font-semibold text-gray-800 mb-2">Generating Labels...</p>
                    <p class="text-sm text-gray-600">Please wait, this may take a few seconds</p>
                </div>
            </div>

            <!-- Information Panel -->
            <div class="bg-blue-50 rounded-xl border border-blue-200 p-6 mt-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-4">
                    <i class="fas fa-info-circle mr-2"></i>Informasi
                </h3>
                <div class="space-y-3 text-sm text-blue-800">
                    <div class="flex items-start space-x-2">
                        <i class="fas fa-check-circle mt-1"></i>
                        <p>Label box akan dibuat sesuai format standar DINAS PENANAMAN MODAL DAN PTSP.</p>
                    </div>
                    <div class="flex items-start space-x-2">
                        <i class="fas fa-check-circle mt-1"></i>
                        <p>Setiap label berisi informasi nomor arsip (dibagi dua sesuai jumlah arsip dalam box), dan nomor box.</p>
                    </div>
                    <div class="flex items-start space-x-2">
                        <i class="fas fa-check-circle mt-1"></i>
                        <p>Box kosong akan menampilkan "NO.ARSIP -" untuk nomor arsip.</p>
                    </div>
                    <div class="flex items-start space-x-2">
                        <i class="fas fa-check-circle mt-1"></i>
                        <p>Gunakan Preview untuk melihat hasil sebelum download.</p>
                    </div>
                    <div class="flex items-start space-x-2">
                        <i class="fas fa-exclamation-triangle mt-1"></i>
                        <p>Format PDF dengan garis potong untuk memudahkan pemisahan label.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .label-preview {
            display: flex;
            justify-content: center;
            animation: fadeInScale 0.5s ease-out;
        }
        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        .label {
            width: 350px;
            height: 140px;
            background-color: white;
            border: 2px solid #000000;
            position: relative;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 30px;
        }
        .header {
            height: 45px;
            background: linear-gradient(135deg, #1e3a8a 0%, #3730a3 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-bottom: 2px solid #ffffff;
        }
        .header-text {
            color: #ffffff;
            font-weight: bold;
            font-size: 13px;
            text-align: center;
            margin: 0;
            line-height: 1.3;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }
        .content {
            height: 95px;
            display: flex;
        }
        .file-numbers {
            flex: 0 0 70%;
            background-color: white;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            border-right: 2px solid #000000;
            padding: 8px;
        }
        .box-number {
            flex: 0 0 30%;
            background-color: white;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            padding: 8px;
        }
        .content-text {
            color: #ffffff;
            font-weight: bold;
            font-size: 15px;
            text-align: center;
            margin: 0;
            line-height: 1.3;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }
        .file-range {
            margin: 6px 0;
            text-align: left;
            padding-left: 15px;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#rack_id').select2({
                placeholder: 'Pilih rack untuk generate label',
                allowClear: true,
                width: '100%'
            });

            // Preview toggle functionality with smooth animation
            let previewVisible = false;
            $('#previewBtn').click(function() {
                const previewSection = $('#previewSection');
                const eyeIcon = $('#eyeIcon');
                const previewText = $('#previewText');

                if (previewVisible) {
                    previewSection.fadeOut(300, function() {
                        $(this).addClass('hidden');
                    });
                    eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                    previewText.text('Preview Format');
                    previewVisible = false;
                } else {
                    previewSection.removeClass('hidden').hide().fadeIn(300);
                    eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
                    previewText.text('Hide Preview');
                    previewVisible = true;
                }
            });

            // Form submission with loading
            $('#generateForm').submit(function(e) {
                e.preventDefault();

                const form = $(this);
                const loadingOverlay = $('#loadingOverlay');
                const generateBtn = $('#generateBtn');

                // Show loading with animation
                loadingOverlay.removeClass('hidden').fadeIn(200);
                generateBtn.prop('disabled', true);

                // Submit form via AJAX
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            // Show success message with download link
                            Swal.fire({
                                title: 'Success!',
                                text: 'Labels generated successfully! Click the link below to download.',
                                icon: 'success',
                                // confirmButtonText: 'Selesai',
                                showCancelButton: true,
                                cancelButtonText: 'Close',
                                html: '<a href="' + response.download_url + '" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Download PDF</a>'
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || 'Failed to generate labels',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred while generating labels';

                        if (xhr.status === 419) {
                            errorMessage = 'Session expired. Please refresh the page and try again.';
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        console.error('Form submission error:', xhr.responseJSON);
                        console.error('HTTP Status:', xhr.status);
                        console.error('Response Text:', xhr.responseText);

                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (xhr.status === 419) {
                                // Refresh page if session expired
                                window.location.reload();
                            }
                        });
                    },
                    complete: function() {
                        // Hide loading with animation
                        loadingOverlay.fadeOut(200, function() {
                            $(this).addClass('hidden');
                        });
                        generateBtn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
</x-app-layout>
