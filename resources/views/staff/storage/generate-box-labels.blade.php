<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-tags text-teal-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Generate Nomor Label</h2>
                        <p class="text-gray-600 mt-1 text-sm">
                            <i class="fas fa-file-alt mr-2"></i>Generate label box arsip untuk pencetakan
                        </p>
                    </div>
                </div>
                <div class="hidden md:block flex items-center space-x-3">
                    <div class="bg-teal-50 rounded-lg p-3">
                        <div class="text-teal-900 text-center">
                            <div class="text-lg font-bold">{{ \App\Models\StorageRack::count() }}</div>
                            <div class="text-xs">Total RAK</div>
                        </div>
                    </div>
                    <!-- Info Fitur Button -->
                    <button type="button" onclick="showFeatureInfo()"
                        class="inline-flex items-center px-4 py-2 bg-teal-100 hover:bg-teal-200 text-teal-700 rounded-lg transition-colors">
                        <i class="fas fa-question-circle mr-2"></i>
                        Info Fitur
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-6 space-y-8">
        <!-- Generate Label Form -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-teal-50 to-blue-50 px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-cog mr-3 text-teal-500"></i>Pengaturan Generate Label
                </h3>
            </div>

            <div class="p-8">
                <form id="generateForm" class="space-y-8">
                    @csrf

                    <!-- Rack Selection -->
                    <div class="space-y-3">
                        <label for="rack_id" class="block text-sm font-semibold text-gray-700">
                            <i class="fas fa-archive mr-2 text-indigo-500"></i>Pilih RAK
                        </label>
                        <select name="rack_id" id="rack_id" required
                            class="w-full bg-white border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all duration-200 py-3 px-4 text-base hover:border-teal-400">
                            <option value="">-- Pilih RAK --</option>
                            @foreach ($racks as $rack)
                                @php
                                    $boxCount = \App\Models\StorageBox::where('rack_id', $rack->id)->count();
                                    $filledBoxCount = \App\Models\StorageBox::where('rack_id', $rack->id)
                                        ->where('archive_count', '>', 0)
                                        ->count();
                                @endphp
                                <option value="{{ $rack->id }}">{{ $rack->name }}
                                    ({{ $filledBoxCount }}/{{ $boxCount }} box)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Box Range Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <label for="box_start" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-sort-numeric-up mr-2 text-green-500"></i>Dari Box
                            </label>
                            <select name="box_start" id="box_start" required
                                class="w-full bg-white border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 py-3 px-4 text-base hover:border-green-400">
                                <option value="">-- Pilih Box --</option>
                            </select>
                        </div>
                        <div class="space-y-3">
                            <label for="box_end" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-sort-numeric-down mr-2 text-red-500"></i>Sampai Box
                            </label>
                            <select name="box_end" id="box_end" required
                                class="w-full bg-white border-2 border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 py-3 px-4 text-base hover:border-red-400">
                                <option value="">-- Pilih Box --</option>
                            </select>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-between items-center pt-4">
                        <button type="button" onclick="syncStorageBoxCounts()"
                            class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors">
                            <i class="fas fa-database mr-2"></i>Sync Counts
                        </button>

                        <button type="submit" id="generateBtn"
                            class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-teal-600 to-blue-600 hover:from-teal-700 hover:to-blue-700 text-white rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-download mr-3"></i>
                            Generate & Download
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Preview Section -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-teal-50 to-blue-50 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-eye mr-3 text-teal-500"></i>Preview Format Label
                    </h3>
                    <button type="button" id="togglePreview"
                        class="inline-flex items-center px-4 py-2 bg-teal-500 hover:bg-teal-600 text-white rounded-lg transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-eye-slash mr-2"></i>
                        <span id="toggleText">Tampilkan Preview</span>
                    </button>
                </div>
            </div>

            <div id="previewContent" class="hidden p-8">
                <div class="bg-gradient-to-br from-orange-50 to-teal-50 p-8 rounded-xl border border-orange-200">
                    <div class="max-w-md mx-auto">
                        <div class="label-preview">
                            <div class="label">
                                <div class="header">
                                    <p class="header-text">DINAS PENANAMAN MODAL DAN PTSP</p>
                                    <p class="header-text">PROVINSI JAWA TIMUR</p>
                                </div>
                                <div class="content">
                                    <div class="file-numbers">
                                        <div class="label-title">NOMOR BERKAS</div>
                                        <div class="file-range">
                                            <p class="content-text">TAHUN 2024 NO. ARSIP 1-8</p>
                                        </div>
                                        <div class="file-range">
                                            <p class="content-text">TAHUN 2025 NO. ARSIP 1-5</p>
                                        </div>
                                    </div>
                                    <div class="box-number">
                                        <div class="label-title">NO. BOKS</div>
                                        <p class="content-text">1</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .label-preview {
            display: flex;
            flex-direction: column;
            align-items: center;
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
            margin-bottom: 80px;
        }

        .header {
            height: 45px;
            background-color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-bottom: 2px solid #000000;
            font-weight: bold;
            font-size: 14px;
            text-align: center;
        }

        .header-text {
            color: #000000;
            font-weight: bold;
            font-size: 12px;
            text-align: center;
            margin: 0;
            line-height: 1.2;
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
            color: #000000;
            font-weight: bold;
            font-size: 10px;
            text-align: center;
            margin: 0;
            line-height: 1.2;
        }

        .file-range {
            margin: 4px 0;
            text-align: center;
            width: 100%;
        }

        .label-title {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 6px;
            color: #000000;
        }
    </style>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                // Remove Select2 initialization - using regular dropdowns

                // Handle rack selection
                $('#rack_id').on('change', function() {
                    const rackId = $(this).val();
                    const boxStartSelect = $('#box_start');
                    const boxEndSelect = $('#box_end');

                    // Reset box selects
                    boxStartSelect.empty().append('<option value="">-- Pilih Box --</option>');
                    boxEndSelect.empty().append('<option value="">-- Pilih Box --</option>');

                    if (rackId) {
                        // Get boxes for selected rack
                        $.get(`/staff/generate-labels/boxes/${rackId}`, function(response) {
                            if (response.success) {
                                response.boxes.forEach(function(box) {
                                    const option = new Option(`Box ${box.box_number}`, box
                                        .box_number);
                                    boxStartSelect.append(option);
                                    boxEndSelect.append(new Option(`Box ${box.box_number}`, box
                                        .box_number));
                                });
                            }
                        }).fail(function(xhr, status, error) {
                            console.error('Error loading boxes:', error);
                        });
                    }
                });

                // Handle box range selection
                $('#box_start, #box_end').on('change', function() {
                    updatePreview();
                });

                // Toggle preview
                $('#togglePreview').on('click', function() {
                    const previewContent = $('#previewContent');
                    const toggleText = $('#toggleText');
                    const icon = $(this).find('i');

                    if (previewContent.hasClass('hidden')) {
                        previewContent.removeClass('hidden').addClass('block');
                        toggleText.text('Sembunyikan Preview');
                        icon.removeClass('fa-eye').addClass('fa-eye-slash');
                    } else {
                        previewContent.removeClass('block').addClass('hidden');
                        toggleText.text('Tampilkan Preview');
                        icon.removeClass('fa-eye-slash').addClass('fa-eye');
                    }
                });

                // Handle form submission
                $('#generateForm').on('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const generateBtn = $('#generateBtn');
                    const originalText = generateBtn.html();

                    // Show loading state
                    generateBtn.prop('disabled', true).html(
                        '<i class="fas fa-spinner fa-spin mr-2"></i>Generating...');

                    // Show loading notification
                    Swal.fire({
                        title: 'Generating Labels...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '/staff/generate-labels/generate',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message,
                                    showCancelButton: true,
                                    confirmButtonText: 'Download File',
                                    cancelButtonText: 'Tutup',
                                    confirmButtonColor: '#10b981',
                                    cancelButtonColor: '#6b7280'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Create temporary link and trigger download
                                        const link = document.createElement('a');
                                        link.href = response.download_url;
                                        link.download = '';
                                        document.body.appendChild(link);
                                        link.click();
                                        document.body.removeChild(link);
                                    }
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'Terjadi kesalahan saat generate label';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: errorMessage
                            });
                        },
                        complete: function() {
                            // Reset button state
                            generateBtn.prop('disabled', false).html(originalText);
                        }
                    });
                });

                function updatePreview() {
                    const rackId = $('#rack_id').val();
                    const boxStart = $('#box_start').val();
                    const boxEnd = $('#box_end').val();

                    if (rackId && boxStart && boxEnd) {
                        // Show loading state for preview
                        $('#previewContent').find('.label-preview').html(`
                            <div class="text-center py-8">
                                <i class="fas fa-spinner fa-spin text-2xl text-blue-500 mb-2"></i>
                                <div class="text-gray-600">Loading preview...</div>
                            </div>
                        `);

                        // Update preview with actual data
                        $.get(`/staff/generate-labels/preview/${rackId}/${boxStart}/${boxEnd}`, function(response) {
                            if (response.success) {
                                // Generate preview HTML based on actual data
                                let previewHtml = '';
                                response.labels.forEach(function(label, index) {
                                    if (index < 3) { // Show first 3 labels for preview
                                        previewHtml += `
                                            <div class="label" style="margin-bottom: 30px;">
                                                <div class="header">
                                                    <p class="header-text">DINAS PENANAMAN MODAL DAN PTSP</p>
                                                    <p class="header-text">PROVINSI JAWA TIMUR</p>
                                                </div>
                                                <div class="content">
                                                    <div class="file-numbers">
                                                        <div class="label-title">NOMOR BERKAS</div>
                                                        ${label.ranges.map(range => `
                                                                                            <div class="file-range">
                                                                                                <p class="content-text">${range}</p>
                                                                                            </div>
                                                                                        `).join('')}
                                                    </div>
                                                    <div class="box-number">
                                                        <div class="label-title">NO. BOKS</div>
                                                        <p class="content-text">${label.box_number}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                    }
                                });
                                $('#previewContent').find('.label-preview').html(previewHtml);
                            } else {
                                // Show error message
                                $('#previewContent').find('.label-preview').html(`
                                    <div class="text-center py-8">
                                        <i class="fas fa-exclamation-triangle text-2xl text-red-500 mb-2"></i>
                                        <div class="text-red-600">${response.message || 'Error loading preview'}</div>
                                    </div>
                                `);
                            }
                        }).fail(function(xhr, status, error) {
                            console.error('Error updating preview:', error);
                            // Show default preview on error
                            $('#previewContent').find('.label-preview').html(`
                                <div class="label">
                                    <div class="header">
                                        <p class="header-text">DINAS PENANAMAN MODAL DAN PTSP</p>
                                        <p class="header-text">PROVINSI JAWA TIMUR</p>
                                    </div>
                                    <div class="content">
                                        <div class="file-numbers">
                                            <div class="label-title">NOMOR BERKAS</div>
                                            <div class="file-range">
                                                <p class="content-text">TAHUN X NO. ARSIP X-X</p>
                                            </div>
                                        </div>
                                        <div class="box-number">
                                            <div class="label-title">NO. BOKS</div>
                                            <p class="content-text">${boxStart}</p>
                                        </div>
                                    </div>
                                </div>
                            `);
                        });
                    } else {
                        // Show default preview when not all fields are selected
                        $('#previewContent').find('.label-preview').html(`
                            <div class="label">
                                <div class="header">
                                    <p class="header-text">DINAS PENANAMAN MODAL DAN PTSP</p>
                                    <p class="header-text">PROVINSI JAWA TIMUR</p>
                                </div>
                                <div class="content">
                                    <div class="file-numbers">
                                        <div class="label-title">NOMOR BERKAS</div>
                                        <div class="file-range">
                                            <p class="content-text">TAHUN X NO. ARSIP X-X</p>
                                        </div>
                                    </div>
                                    <div class="box-number">
                                        <div class="label-title">NO. BOKS</div>
                                        <p class="content-text">X</p>
                                    </div>
                                </div>
                            </div>
                        `);
                    }
                }
            });
        </script>

        <!-- Info Fitur Modal -->
        <script>
            function showFeatureInfo() {
                const html = `
                    <div class="text-left space-y-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-semibold text-blue-800 mb-2 flex items-center">
                                <i class="fas fa-tags mr-2"></i>
                                Fitur Generate Box Labels
                            </h4>
                            <ul class="list-disc ml-5 text-sm text-blue-700 space-y-1">
                                <li><strong>Pilih Rak:</strong> Pilih rak yang akan di-generate labelnya</li>
                                <li><strong>Range Box:</strong> Tentukan range box dari mana sampai mana</li>
                                <li><strong>Preview Label:</strong> Lihat preview label sebelum download</li>
                                <li><strong>Download PDF:</strong> Download label dalam format PDF untuk pencetakan</li>
                            </ul>
                        </div>

                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h4 class="font-semibold text-green-800 mb-2 flex items-center">
                                <i class="fas fa-eye mr-2"></i>
                                Fitur Preview
                            </h4>
                            <ul class="list-disc ml-5 text-sm text-green-700 space-y-1">
                                <li><strong>Real-time Preview:</strong> Preview label otomatis update saat pilih rak dan box</li>
                                <li><strong>Format Label:</strong> Label sesuai standar DPMPTSP Provinsi Jawa Timur</li>
                                <li><strong>Nomor Berkas:</strong> Menampilkan range nomor berkas dalam box</li>
                                <li><strong>Nomor Box:</strong> Menampilkan nomor box yang dipilih</li>
                            </ul>
                        </div>

                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <h4 class="font-semibold text-yellow-800 mb-2 flex items-center">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Perhatian Khusus
                            </h4>
                            <ul class="list-disc ml-5 text-sm text-yellow-700 space-y-1">
                                <li><strong>Range Box:</strong> Pastikan range box sudah benar sebelum generate</li>
                                <li><strong>Kapasitas Box:</strong> Box kosong tidak akan menampilkan nomor berkas</li>
                                <li><strong>Format PDF:</strong> Label akan di-generate dalam format PDF standar</li>
                                <li><strong>Preview:</strong> Selalu lihat preview sebelum download untuk memastikan akurasi</li>
                            </ul>
                        </div>

                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <h4 class="font-semibold text-purple-800 mb-2 flex items-center">
                                <i class="fas fa-lightbulb mr-2"></i>
                                Tips Penggunaan
                            </h4>
                            <ul class="list-disc ml-5 text-sm text-purple-700 space-y-1">
                                <li>Pilih rak yang memiliki box dengan arsip untuk hasil yang optimal</li>
                                <li>Gunakan range box yang berurutan untuk kemudahan pencetakan</li>
                                <li>Lihat preview untuk memastikan label sudah sesuai sebelum download</li>
                                <li>Simpan file PDF dengan nama yang jelas untuk kemudahan identifikasi</li>
                            </ul>
                        </div>
                    </div>
                `;

                Swal.fire({
                    title: 'Panduan Fitur: Generate Box Labels',
                    html: html,
                    width: '700px',
                    confirmButtonText: 'Saya Mengerti',
                    confirmButtonColor: '#3b82f6',
                    showCloseButton: true,
                    customClass: {
                        container: 'swal2-custom-container',
                        popup: 'swal2-custom-popup'
                    }
                });
            }

            // Sync storage box counts function
            function syncStorageBoxCounts() {
                // Show loading
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sinkronisasi storage box counts',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Call sync counts via AJAX
                fetch('{{ route('staff.generate-labels.sync-counts') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Refresh rack options with updated counts
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat sinkronisasi',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
            }
        </script>
    @endpush
</x-app-layout>
