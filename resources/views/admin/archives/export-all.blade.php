<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Export Arsip Excel</h1>
                <p class="text-sm text-gray-600 mt-1">Filter dan ekspor data arsip ke format Excel</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.dashboard') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

            <!-- Export Form -->
            <form action="{{ route('admin.archives.export') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Filter Section -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-filter mr-2 text-blue-500"></i>
                        Filter Data Export
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-flag mr-2 text-green-500"></i>Status Arsip
                            </label>
                            <select name="status" id="status"
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4 select2-export">
                                <option value="all">Semua Status</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category_filter" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-folder mr-2 text-indigo-500"></i>Kategori
                            </label>
                            <select name="category_filter" id="category_filter"
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4 select2-export">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Classification -->
                        <div>
                            <label for="classification_filter" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-tags mr-2 text-cyan-500"></i>Klasifikasi
                            </label>
                            <select name="classification_filter" id="classification_filter"
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4 select2-export">
                                <option value="">Semua Klasifikasi</option>
                                @foreach($classifications as $classification)
                                    <option value="{{ $classification->id }}" data-category-id="{{ $classification->category_id }}">
                                        {{ $classification->code }} - {{ $classification->nama_klasifikasi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date From -->
                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt mr-2 text-orange-500"></i>Tanggal Dari
                            </label>
                            <input type="date" name="date_from" id="date_from"
                                   class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                        </div>

                        <!-- Date To -->
                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt mr-2 text-orange-500"></i>Tanggal Sampai
                            </label>
                            <input type="date" name="date_to" id="date_to"
                                   class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                        </div>

                        <!-- Created By -->
                        <div>
                            <label for="created_by_filter" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user mr-2 text-purple-500"></i>Dibuat Oleh
                            </label>
                            <select name="created_by_filter" id="created_by_filter"
                                    class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4 select2-export">
                                <option value="">Semua User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Export Options -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-cogs mr-2 text-green-500"></i>
                        Opsi Export
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Include Headers -->
                        <div class="flex items-center">
                            <input type="checkbox" name="include_headers" id="include_headers" value="1" checked
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                            <label for="include_headers" class="ml-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-table mr-2 text-blue-500"></i>Sertakan Header Kolom
                            </label>
                        </div>

                        <!-- Include Statistics -->
                        <div class="flex items-center">
                            <input type="checkbox" name="include_stats" id="include_stats" value="1"
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                            <label for="include_stats" class="ml-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-chart-bar mr-2 text-green-500"></i>Sertakan Statistik
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Section -->
                <div class="pt-6 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl transition-colors shadow-sm">
                                <i class="fas fa-download mr-2"></i>
                                Download Excel
                            </button>
                            <button type="button" id="previewBtn"
                                    class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors">
                                <i class="fas fa-eye mr-2"></i>
                                Preview Data
                            </button>
                        </div>
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Filter sesuai kebutuhan sebelum export
                        </div>
                    </div>
                </div>
            </form>

            <!-- Preview Section -->
            <div id="previewSection" class="mt-8 hidden">
                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-eye mr-2 text-blue-500"></i>
                    Preview Data Export
                </h4>
                <div id="previewContent" class="bg-gray-50 p-4 rounded-xl">
                    <!-- Preview will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .select2-container--default .select2-selection--single {
                height: 56px !important;
                border: 1px solid #d1d5db !important;
                border-radius: 0.75rem !important;
                padding: 0.75rem 1rem !important;
                line-height: 40px !important;
            }
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 40px !important;
                padding-left: 0 !important;
                color: #374151 !important;
                text-align: center !important;
            }
            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 54px !important;
                right: 12px !important;
            }
            .select2-container--default.select2-container--focus .select2-selection--single {
                border-color: #3b82f6 !important;
                box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5) !important;
            }
            .select2-dropdown {
                border-radius: 0.75rem !important;
                border: 1px solid #d1d5db !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                // Initialize Select2
                $('.select2-export').select2({
                    placeholder: function() {
                        return $(this).find('option[value=""]').text() || $(this).find('option:first').text();
                    },
                    allowClear: true,
                    width: '100%'
                });

                // Handle category-classification dependencies
                const allClassifications = @json($classifications);

                $('#category_filter').on('change', function() {
                    const categoryId = $(this).val();
                    const classificationSelect = $('#classification_filter');

                    classificationSelect.empty();
                    classificationSelect.append('<option value="">Semua Klasifikasi</option>');

                    if (categoryId) {
                        const filteredClassifications = allClassifications.filter(c => c.category_id == categoryId);
                        filteredClassifications.forEach(function(classification) {
                            classificationSelect.append(new Option(`${classification.code} - ${classification.name}`, classification.id));
                        });
                    } else {
                        allClassifications.forEach(function(classification) {
                            classificationSelect.append(new Option(`${classification.code} - ${classification.name}`, classification.id));
                        });
                    }

                    classificationSelect.select2({
                        placeholder: "Semua Klasifikasi",
                        allowClear: true,
                        width: '100%'
                    });
                });

                $('#classification_filter').on('change', function() {
                    const classificationId = $(this).val();

                    if (classificationId) {
                        const selectedClassification = allClassifications.find(c => c.id == classificationId);
                        if (selectedClassification && $('#category_filter').val() != selectedClassification.category_id) {
                            $('#category_filter').val(selectedClassification.category_id).trigger('change.select2');
                        }
                    }
                });

                // Preview functionality
                $('#previewBtn').on('click', function() {
                    const formData = new FormData($('form')[0]);

                    // Show preview section
                    $('#previewSection').removeClass('hidden');
                    $('#previewContent').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat preview...</div>');

                    // Simulate preview (you can implement actual preview via AJAX)
                    setTimeout(function() {
                        const selectedFilters = [];
                        if ($('#status').val() && $('#status').val() !== 'all') selectedFilters.push(`Status: ${$('#status').val()}`);
                        if ($('#category_filter').val()) selectedFilters.push(`Kategori: ${$('#category_filter option:selected').text()}`);
                        if ($('#classification_filter').val()) selectedFilters.push(`Klasifikasi: ${$('#classification_filter option:selected').text()}`);
                        if ($('#date_from').val()) selectedFilters.push(`Dari: ${$('#date_from').val()}`);
                        if ($('#date_to').val()) selectedFilters.push(`Sampai: ${$('#date_to').val()}`);
                        if ($('#created_by_filter').val()) selectedFilters.push(`Dibuat oleh: ${$('#created_by_filter option:selected').text()}`);

                        const previewHtml = `
                            <div class="space-y-4">
                                <div>
                                    <h5 class="font-medium text-gray-900 mb-2">Filter yang Diterapkan:</h5>
                                    <div class="flex flex-wrap gap-2">
                                        ${selectedFilters.length > 0
                                            ? selectedFilters.map(filter => `<span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">${filter}</span>`).join('')
                                            : '<span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm">Tidak ada filter</span>'
                                        }
                                    </div>
                                </div>
                                <div class="text-sm text-gray-600">
                                    <p><strong>Kolom yang akan diekspor:</strong> No. Arsip, Uraian, Kategori, Klasifikasi, Tanggal Arsip, Status, Dibuat Oleh, Tanggal Dibuat</p>
                                </div>
                            </div>
                        `;
                        $('#previewContent').html(previewHtml);
                    }, 1000);
                });
            });
        </script>
    @endpush
</x-app-layout>
