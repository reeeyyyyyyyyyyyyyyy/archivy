<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-orange-600 to-pink-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-excel text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">
                            Preview Export Data Arsip
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-tag mr-1"></i>Status: {{ $statusTitle }}
                            <span class="mx-2">•</span>
                            <i class="fas fa-user mr-1"></i>Intern
                            <span class="mx-2">•</span>
                            <i class="fas fa-calendar mr-1"></i>{{ now()->format('d F Y') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('intern.export.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Export Form Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <!-- Card Header -->
                <div class="bg-gradient-to-r from-orange-50 to-pink-100 px-8 py-6 border-b border-orange-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div
                                class="w-16 h-16 bg-gradient-to-br from-orange-600 to-pink-600 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-download text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Preview Export Data Arsip</h3>
                                <p class="text-gray-600 mt-1">Preview fitur export untuk pembelajaran (mode intern)</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-orange-600">{{ $statusTitle }}</div>
                            <div class="text-sm text-gray-500">Status Dipilih</div>
                        </div>
                    </div>
                </div>

                <!-- Form Content -->
                <form action="{{ route('intern.archives.export.process') }}" method="POST" class="p-8">
                    @csrf
                    <input type="hidden" name="status" value="{{ $status }}">

                    <!-- Info Panel -->
                    <div class="mb-8 p-6 bg-blue-50 border border-blue-200 rounded-xl">
                        <div class="flex items-start space-x-4">
                            <div
                                class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-info-circle text-white"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-blue-900 mb-2">Preview Mode - Informasi Export</h4>
                                <div class="text-sm text-blue-800 space-y-1">
                                    <p>• <strong>PREVIEW MODE:</strong> Fitur ini hanya untuk pembelajaran</p>
                                    <p>• Status arsip: <strong>{{ $statusTitle }}</strong></p>
                                    <p>• Anda dapat melihat bagaimana filter periode tahun bekerja</p>
                                    <p>• <strong>Export tidak akan menghasilkan file</strong> - hanya preview</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Options -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <!-- Year Range Filter -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-calendar-alt mr-2 text-orange-500"></i>
                                Filter Periode Tahun
                            </h4>

                            <div class="space-y-4">
                                <div>
                                    <label for="year_from" class="block text-sm font-medium text-gray-700 mb-2">
                                        Dari Tahun
                                    </label>
                                    <input type="number" name="year_from" id="year_from" min="2000"
                                        max="{{ date('Y') + 5 }}" value="{{ request('year_from', date('Y') - 1) }}"
                                        class="w-full border-gray-300 rounded-lg focus:border-orange-500 focus:ring-orange-500"
                                        placeholder="2023">
                                </div>

                                <div>
                                    <label for="year_to" class="block text-sm font-medium text-gray-700 mb-2">
                                        Sampai Tahun
                                    </label>
                                    <input type="number" name="year_to" id="year_to" min="2000"
                                        max="{{ date('Y') + 5 }}" value="{{ request('year_to', date('Y')) }}"
                                        class="w-full border-gray-300 rounded-lg focus:border-orange-500 focus:ring-orange-500"
                                        placeholder="2024">
                                </div>

                                <!-- Quick Year Buttons -->
                                <div class="flex flex-wrap gap-2">
                                    <button type="button"
                                        onclick="setYearRange({{ date('Y') }}, {{ date('Y') }})"
                                        class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded border">
                                        {{ date('Y') }}
                                    </button>
                                    <button type="button"
                                        onclick="setYearRange({{ date('Y') - 1 }}, {{ date('Y') }})"
                                        class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded border">
                                        {{ date('Y') - 1 }}-{{ date('Y') }}
                                    </button>
                                    <button type="button"
                                        onclick="setYearRange({{ date('Y') - 4 }}, {{ date('Y') }})"
                                        class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded border">
                                        5 Tahun Terakhir
                                    </button>
                                    <button type="button" onclick="clearYearRange()"
                                        class="px-2 py-1 text-xs bg-red-100 hover:bg-red-200 text-red-600 rounded border">
                                        Reset
                                    </button>
                                </div>

                                <div class="text-xs text-gray-500 p-3 bg-gray-50 rounded-lg">
                                    <i class="fas fa-lightbulb mr-1"></i>
                                    <strong>Tips:</strong> Kosongkan tahun untuk export semua data Anda tanpa filter
                                    periode
                                </div>
                            </div>
                        </div>

                        <!-- Category and Classification Filters -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-filter mr-2 text-pink-500"></i>
                                Filter Kategori & Klasifikasi
                            </h4>

                            <div class="space-y-4">
                                <!-- Category Filter -->
                                <div>
                                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-folder mr-1"></i>Kategori
                                    </label>
                                    <select name="category_id" id="category_id" 
                                        class="w-full border-gray-300 rounded-lg focus:border-orange-500 focus:ring-orange-500">
                                        <option value="">Pilih Kategori</option>
                                        @foreach (\App\Models\Category::orderBy('nama_kategori')->get() as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Classification Filter -->
                                <div>
                                    <label for="classification_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-tags mr-1"></i>Klasifikasi
                                    </label>
                                    <select name="classification_id" id="classification_id" 
                                        class="w-full border-gray-300 rounded-lg focus:border-orange-500 focus:ring-orange-500">
                                        <option value="">Cari Klasifikasi</option>
                                    </select>
                                </div>

                                <div class="text-xs text-gray-500 p-3 bg-gray-50 rounded-lg">
                                    <i class="fas fa-lightbulb mr-1"></i>
                                    <strong>Tips:</strong> Pilih kategori terlebih dahulu untuk memfilter klasifikasi
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Export Options -->
                    <div class="mb-8 p-6 bg-gray-50 border border-gray-200 rounded-xl">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-cogs mr-2 text-gray-600"></i>
                            Opsi Export
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Include Headers -->
                            <div class="flex items-center">
                                <input type="checkbox" name="include_headers" id="include_headers" value="1"
                                    checked
                                    class="w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500 focus:ring-2">
                                <label for="include_headers" class="ml-2 text-sm text-gray-900">
                                    Sertakan header kolom
                                </label>
                            </div>

                            <!-- Include Timestamps -->
                            <div class="flex items-center">
                                <input type="checkbox" name="include_timestamps" id="include_timestamps"
                                    value="1" checked
                                    class="w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500 focus:ring-2">
                                <label for="include_timestamps" class="ml-2 text-sm text-gray-900">
                                    Sertakan tanggal dibuat/diupdate
                                </label>
                            </div>

                            <!-- Auto-fit columns -->
                            <div class="flex items-center">
                                <input type="checkbox" name="auto_fit_columns" id="auto_fit_columns" value="1"
                                    checked
                                    class="w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500 focus:ring-2">
                                <label for="auto_fit_columns" class="ml-2 text-sm text-gray-900">
                                    Sesuaikan lebar kolom otomatis
                                </label>
                            </div>
                        </div>

                        <!-- File naming info -->
                        <div class="text-xs text-gray-500 p-3 bg-white rounded-lg mt-4">
                            <i class="fas fa-file-signature mr-1"></i>
                            <strong>Nama File:</strong>
                            arsip-{{ strtolower($statusTitle) }}-{{ auth()->user()->name }}-{{ date('Y-m-d') }}.xlsx
                        </div>
                    </div>
                    
                    <!-- Summary Statistics Preview -->
                    <div
                        class="mb-8 p-6 bg-gradient-to-r from-orange-50 to-pink-50 border border-orange-200 rounded-xl">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-chart-bar mr-2 text-orange-600"></i>
                            Ringkasan Data yang Akan Diexport
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <div class="text-2xl font-bold text-orange-600">{{ $totalRecords ?? 0 }}</div>
                                <div class="text-sm text-gray-600">Total Arsip Saya</div>
                                <div class="text-xs text-gray-500 mt-1">Status: {{ $statusTitle }}</div>
                            </div>
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <div class="text-2xl font-bold text-pink-600">{{ auth()->user()->name }}</div>
                                <div class="text-sm text-gray-600">Dibuat Oleh</div>
                                <div class="text-xs text-gray-500 mt-1">Intern</div>
                            </div>
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <div class="text-2xl font-bold text-orange-600">
                                    <i class="fas fa-file-excel"></i>
                                </div>
                                <div class="text-sm text-gray-600">Format File</div>
                                <div class="text-xs text-gray-500 mt-1">Microsoft Excel</div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('intern.export.index') }}"
                            class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali ke Menu
                        </a>

                        <button type="submit"
                            class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-orange-600 to-pink-600 hover:from-orange-700 hover:to-pink-700 text-white font-semibold rounded-lg transition-all shadow-lg hover:shadow-xl">
                            <i class="fas fa-eye mr-2"></i>
                            Preview Export
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .select2-container .select2-selection--single {
                height: 42px !important;
                border: 1px solid #d1d5db !important;
                border-radius: 0.5rem !important;
                background-color: white !important;
            }
            
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 40px !important;
                color: #374151 !important;
                padding-left: 12px !important;
                padding-right: 30px !important;
                font-size: 14px !important;
            }
            
            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 40px !important;
                right: 8px !important;
            }
            
            .select2-container--default.select2-container--focus .select2-selection--single {
                border-color: #f97316 !important;
                outline: 0 !important;
                box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1) !important;
            }
            
            .select2-container {
                width: 100% !important;
            }
            
            .select2-dropdown {
                border: 1px solid #d1d5db !important;
                border-radius: 0.5rem !important;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            // Set year range for quick buttons
            function setYearRange(from, to) {
                document.getElementById('year_from').value = from;
                document.getElementById('year_to').value = to;
            }

            // Clear year range
            function clearYearRange() {
                document.getElementById('year_from').value = '';
                document.getElementById('year_to').value = '';
            }

            // Initialize Select2
            $(document).ready(function() {
                $('#classification_id').select2({
                    placeholder: 'Cari Klasifikasi',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#classification_id').parent()
                });

                // AJAX for classification dropdown based on category selection
                $('#category_id').change(function() {
                    const categoryId = this.value;
                    const classificationSelect = $('#classification_id');
                    
                    // Clear classification dropdown
                    classificationSelect.empty().append('<option value="">Semua Klasifikasi</option>');
                    classificationSelect.val('').trigger('change');
                    
                    if (categoryId) {
                        // Show loading indicator
                        classificationSelect.empty().append('<option value="">Memuat klasifikasi...</option>');
                        classificationSelect.prop('disabled', true);
                        
                        // Fetch classifications based on selected category
                        fetch('/intern/archives/api/classifications-by-category/' + categoryId)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            classificationSelect.empty().append('<option value="">Semua Klasifikasi</option>');
                            
                            if (data.length === 0) {
                                classificationSelect.append('<option value="">Tidak ada klasifikasi untuk kategori ini</option>');
                            } else {
                                data.forEach(classification => {
                                    classificationSelect.append(
                                        $('<option>', {
                                            value: classification.id,
                                            text: classification.nama_klasifikasi
                                        })
                                    );
                                });
                            }
                            
                            classificationSelect.prop('disabled', false);
                            classificationSelect.trigger('change');
                        })
                        .catch(error => {
                            console.error('Error fetching classifications:', error);
                            classificationSelect.empty().append('<option value="">Error memuat klasifikasi</option>');
                            classificationSelect.prop('disabled', false);
                        });
                    } else {
                        classificationSelect.prop('disabled', false);
                    }
                });
            });

            // Validate year range on form submit
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.querySelector('form');
                const yearFrom = document.getElementById('year_from');
                const yearTo = document.getElementById('year_to');

                form.addEventListener('submit', function(e) {
                    const fromVal = parseInt(yearFrom.value);
                    const toVal = parseInt(yearTo.value);

                    // If both filled, validate range
                    if (fromVal && toVal && fromVal > toVal) {
                        e.preventDefault();
                        alert('Tahun "Dari" tidak boleh lebih besar dari tahun "Sampai"');
                        return false;
                    }

                    // If only one filled, alert user
                    if ((fromVal && !toVal) || (!fromVal && toVal)) {
                        if (!confirm(
                                'Anda hanya mengisi satu tahun. Lanjutkan export dengan filter tahun tunggal?'
                            )) {
                            e.preventDefault();
                            return false;
                        }
                    }
                });

                // Auto-fill "to" field when "from" is filled
                yearFrom.addEventListener('change', function() {
                    if (this.value && !yearTo.value) {
                        yearTo.value = this.value;
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>