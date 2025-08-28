<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-pink-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-excel text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Export Data Arsip ke Excel</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Export data arsip {{ strtolower($statusTitle) }} ke format Excel
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="text-right">
                        <div class="text-sm text-gray-500">Export Format</div>
                        <div class="flex items-center text-orange-600 font-semibold">
                            <i class="fas fa-file-excel mr-2"></i>
                            Microsoft Excel (.xlsx)
                        </div>
                    </div>
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
                            <div class="w-16 h-16 bg-gradient-to-br from-orange-600 to-pink-600 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-download text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Pengaturan Export Data</h3>
                                <p class="text-gray-600 mt-1">Sesuaikan filter dan parameter export sesuai kebutuhan</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-pink-600">{{ $statusTitle }}</div>
                            <div class="text-sm text-gray-500">Status Dipilih</div>
                        </div>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-8">
                    <!-- Quick Export Button -->
                    <div class="mb-8 p-6 bg-gradient-to-r from-orange-50 to-pink-100 rounded-xl border border-orange-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-bolt text-orange-600 text-3xl mr-4"></i>
                                <div>
                                    <h4 class="text-lg font-bold text-gray-900">Export Cepat</h4>
                                    <p class="text-gray-600">Export semua data {{ strtolower($statusTitle) }} tanpa
                                        filter tambahan</p>
                                </div>
                            </div>
                            <form action="{{ route('intern.archives.export.process') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="{{ $status }}">
                                <button type="submit"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-600 to-pink-600 hover:from-orange-700 hover:to-pink-700 text-white font-semibold rounded-lg transition-colors transform hover:scale-105">
                                    <i class="fas fa-download mr-2"></i>
                                    Export Sekarang
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Detailed Export Form -->
                    <div class="border-t border-gray-200 pt-8">
                        <h4 class="text-lg font-bold text-gray-900 mb-6">
                            <i class="fas fa-filter mr-2 text-pink-600"></i>
                            Export dengan Filter Advanced
                        </h4>

                        <form action="{{ route('intern.archives.export.process') }}" method="POST" class="space-y-6">
                            @csrf
                            <input type="hidden" name="status" value="{{ $status }}">

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Status Info -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-tag mr-1 text-orange-500"></i>Status Arsip
                                    </label>
                                    <div class="p-3 bg-orange-100 rounded-lg border border-orange-200">
                                        <span class="font-semibold text-orange-800">{{ $statusTitle }}</span>
                                    </div>
                                </div>

                                <!-- Created By Filter -->
                                <div>
                                    <label for="created_by" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-user mr-1 text-orange-500"></i>Filter Berdasarkan Pembuat
                                    </label>
                                    <select name="created_by" id="created_by"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                                        <option value="current_user">Data Saya Saja</option>
                                        <option value="">Semua Data (intern)</option>
                                        @foreach (\App\Models\User::whereHas('roles', function($q) {
                                            $q->whereIn('name', ['intern', 'admin']);
                                        })->orderBy('name')->get() as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}
                                                ({{ $user->roles->first()->name ?? 'No Role' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-info-circle text-orange-500 mt-1 mr-2"></i>
                                        <strong>Catatan:</strong>
                                    <ul class="list-disc list-inside space-y-1 text-xs">
                                        <li>Data yang diambil adalah data yang dibuat oleh user yang dipilih</li>
                                        <li>Status arsip akan difilter sesuai dengan status yang dipilih</li>
                                    </ul>
                                    </p>
                                </div>

                                <!-- Year Range Selection -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-calendar-alt mr-1 text-orange-500"></i>Filter Rentang Tahun (Opsional)
                                    </label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label for="year_from" class="block text-xs text-gray-500 mb-1">Dari
                                                Tahun</label>
                                            <input type="number" name="year_from" id="year_from" min="2000"
                                                max="{{ date('Y') + 1 }}" placeholder="2020"
                                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50 text-sm">
                                        </div>
                                        <div>
                                            <label for="year_to" class="block text-xs text-gray-500 mb-1">Sampai
                                                Tahun</label>
                                            <input type="number" name="year_to" id="year_to" min="2000"
                                                max="{{ date('Y') + 1 }}" placeholder="{{ date('Y') }}"
                                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50 text-sm">
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Kosongkan kedua field untuk export semua tahun
                                    </p>

                                    <!-- Quick Year Buttons -->
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        <button type="button"
                                            onclick="setYearRange({{ date('Y') }}, {{ date('Y') }})"
                                            class="px-2 py-1 text-xs bg-orange-100 hover:bg-orange-200 text-orange-700 rounded border border-orange-200">
                                            {{ date('Y') }}
                                        </button>
                                        <button type="button"
                                            onclick="setYearRange({{ date('Y') - 1 }}, {{ date('Y') }})"
                                            class="px-2 py-1 text-xs bg-orange-100 hover:bg-orange-200 text-orange-700 rounded border border-orange-200">
                                            {{ date('Y') - 1 }}-{{ date('Y') }}
                                        </button>
                                        <button type="button"
                                            onclick="setYearRange({{ date('Y') - 4 }}, {{ date('Y') }})"
                                            class="px-2 py-1 text-xs bg-orange-100 hover:bg-orange-200 text-orange-700 rounded border border-orange-200">
                                            5 Tahun Terakhir
                                        </button>
                                        <button type="button" onclick="clearYearRange()"
                                            class="px-2 py-1 text-xs bg-pink-100 hover:bg-pink-200 text-pink-700 rounded border border-pink-200">
                                            Reset
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Category and Classification Filters -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-200">
                                <!-- Category Filter -->
                                <div>
                                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-folder mr-1 text-orange-500"></i>Kategori
                                    </label>
                                    <select name="category_id" id="category_id"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                                        <option value="">Pilih Kategori</option>
                                        @foreach (\App\Models\Category::orderBy('nama_kategori')->get() as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Classification Filter -->
                                <div>
                                    <label for="classification_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-tags mr-1 text-orange-500"></i>Klasifikasi
                                    </label>
                                    <select name="classification_id" id="classification_id"
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                                        <option value="">Cari Klasifikasi</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Preview Info -->
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <i class="fas fa-info-circle text-orange-600 mt-1 mr-2"></i>
                                    <div class="text-sm text-orange-800">
                                        <p class="font-medium mb-2">Format Export Excel:</p>
                                        <ul class="list-disc list-inside space-y-1 text-xs">
                                            <li><strong>Header:</strong> Dinas Penanaman Modal dan Pelayanan Terpadu
                                                Satu Pintu</li>
                                            <li><strong>Kolom Otomatis:</strong> No, Kode Klasifikasi, Indeks, Uraian,
                                                Kurun Waktu, Tingkat Perkembangan, Jumlah, Keterangan, Jangka Simpan
                                            </li>
                                            <li><strong>Kolom Manual:</strong> Nomor Definitif, Nomor Boks, Rak, Baris,
                                                Lokasi Simpan (kosong untuk input manual)</li>
                                            <li><strong>Styling:</strong> Header berwarna oranye, tabel dengan border,
                                                ready untuk print</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <a href="{{ route('intern.export.index') }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-md transition duration-200">
                                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                                </a>

                                <button type="submit"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-600 to-pink-600 hover:from-orange-700 hover:to-pink-700 text-white text-sm font-medium rounded-md transition duration-200 shadow-lg">
                                    <i class="fas fa-download mr-2"></i>Download Excel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            /* Pastikan Select2 memiliki ukuran yang sama dengan input lainnya */
            .select2-container .select2-selection--single {
                height: 38px !important;
                border: 1px solid #d1d5db !important;
                border-radius: 0.375rem !important;
                background-color: white !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 36px !important;
                color: #374151 !important;
                padding-left: 12px !important;
                padding-right: 30px !important;
                font-size: 14px !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 36px !important;
                right: 8px !important;
            }

            .select2-container--default.select2-container--focus .select2-selection--single {
                border-color: #fdba74 !important;
                outline: 0 !important;
                box-shadow: 0 0 0 3px rgba(253, 186, 116, 0.5) !important;
            }

            /* Pastikan lebar konsisten */
            .select2-container {
                width: 100% !important;
            }

            /* Style untuk dropdown */
            .select2-dropdown {
                border: 1px solid #d1d5db !important;
                border-radius: 0.375rem !important;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
            }

            /* Pastikan tinggi input konsisten */
            .border-gray-300 {
                height: 38px;
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

            // Validate year range on form submit
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.querySelector('form');
                const yearFrom = document.getElementById('year_from');
                const yearTo = document.getElementById('year_to');

                // Inisialisasi Select2 HANYA SEKALI di awal
                $(document).ready(function() {
                    $('#classification_id').select2({
                        placeholder: 'Cari Klasifikasi',
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#classification_id').parent()
                    });
                });

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

                // AJAX for classification dropdown based on category selection
                const categorySelect = document.getElementById('category_id');

                categorySelect.addEventListener('change', function() {
                    const categoryId = this.value;
                    const classificationSelect = $('#classification_id');

                    // Clear classification dropdown menggunakan jQuery
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
        </script>
    @endpush
</x-app-layout>
