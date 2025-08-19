<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-pink-500 hover:from-orange-600 hover:to-pink-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Lokasi Penyimpanan</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Atur lokasi penyimpanan arsip Anda
                        </p>
                    </div>
                </div>
                {{-- <a href="{{ route('intern.storage.generate-box-file-numbers') }}"
                    class="inline-flex items-center px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-2xl transition-colors">
                    <i class="fas fa-magic mr-2"></i>Generate Otomatis
                </a> --}}
                {{-- <a href="{{ route('intern.storage.generate-box-labels') }}"
                    class="inline-flex items-center px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl transition-colors">
                    <i class="fas fa-tags mr-2"></i>Generate Label Box
                </a> --}}
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-6 space-y-6">
        <!-- Filter Panel -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-filter mr-2 text-blue-500"></i>Filter Arsip
            </h3>

            <form method="GET" action="{{ route('intern.storage.index') }}"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label for="status_filter" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tag mr-2 text-green-500"></i>Status Arsip
                    </label>
                    <select name="status_filter" id="status_filter"
                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                        <option value="">Semua Status</option>
                        <option value="Aktif" {{ request('status_filter') == 'Aktif' ? 'selected' : '' }}>Arsip Aktif
                        </option>
                        <option value="Inaktif" {{ request('status_filter') == 'Inaktif' ? 'selected' : '' }}>Arsip
                            Inaktif</option>
                        <option value="Permanen" {{ request('status_filter') == 'Permanen' ? 'selected' : '' }}>Arsip
                            Permanen</option>
                        <option value="Musnah" {{ request('status_filter') == 'Musnah' ? 'selected' : '' }}>Arsip Musnah
                        </option>
                        <option value="Dinilai Kembali"
                            {{ request('status_filter') == 'Dinilai Kembali' ? 'selected' : '' }}>Arsip Dinilai Kembali
                        </option>
                    </select>
                </div>

                <div>
                    <label for="category_filter" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-folder mr-2 text-indigo-500"></i>Kategori
                    </label>
                    <select name="category_filter" id="category_filter"
                        class="select2-filter w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories ?? [] as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category_filter') == $category->id ? 'selected' : '' }}>
                                {{ $category->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Classification Filter -->
                <div>
                    <label for="classification_filter" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tags mr-2 text-cyan-500"></i>Klasifikasi
                    </label>
                    <select name="classification_filter" id="classification_filter"
                        class="select2-filter w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                        <option value="">Semua Klasifikasi</option>
                        @foreach ($classifications ?? [] as $classification)
                            <option value="{{ $classification->id }}"
                                {{ request('classification_filter') == $classification->id ? 'selected' : '' }}>
                                {{ $classification->code }} - {{ $classification->nama_klasifikasi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-search mr-2 text-purple-500"></i>Pencarian
                    </label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Cari no. arsip atau uraian..."
                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                </div>

                <div class="flex items-end space-x-3">
                    <button type="submit"
                        class="inline-flex items-center px-4 py-3 bg-gradient-to-r from-orange-500 to-pink-500 hover:from-orange-600 hover:to-pink-600 text-white rounded-lg transition-colors">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                    <a href="{{ route('intern.storage.index') }}"
                        class="inline-flex items-center px-4 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                        <i class="fas fa-times mr-2"></i>Clear
                    </a>
                </div>
            </form>
        </div>

        @if ($archives->isEmpty())
            <div class="text-center py-16">
                <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak ada arsip yang perlu diatur lokasi</h3>
                <p class="text-gray-500">Semua arsip Anda sudah memiliki lokasi penyimpanan atau belum ada arsip yang
                    dibuat.</p>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-list mr-2 text-indigo-500"></i>Arsip Belum Diatur Lokasi
                        ({{ $archives->total() }})
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No. Arsip</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Uraian</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tgl Arsip</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($archives as $archive)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ ($archives->currentPage() - 1) * $archives->perPage() + $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $archive->index_number }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="max-w-xs truncate" title="{{ $archive->description }}">
                                            {{ $archive->description }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusClasses = [
                                                'Aktif' => 'bg-green-100 text-green-800',
                                                'Inaktif' => 'bg-yellow-100 text-yellow-800',
                                                'Permanen' => 'bg-purple-100 text-purple-800',
                                                'Musnah' => 'bg-red-100 text-red-800',
                                                'Dinilai Kembali' => 'bg-indigo-100 text-indigo-800',
                                            ];
                                        @endphp
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$archive->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $archive->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $archive->kurun_waktu_start->format('d-m-Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('intern.storage.create', $archive->id) }}"
                                            class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-orange-500 to-pink-500 hover:from-orange-600 hover:to-pink-600 text-white text-sm rounded-lg transition-colors">
                                            <i class="fas fa-map-marker-alt mr-2"></i>Set Lokasi
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $archives->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Location Modal -->
    <div id="locationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Set Lokasi Penyimpanan</h3>
                <form id="locationForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="box_number" class="block text-sm font-medium text-gray-700 mb-2">Nomor
                            Boks</label>
                        <input type="number" name="box_number" id="box_number" min="1" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label for="rack_number" class="block text-sm font-medium text-gray-700 mb-2">Nomor
                            Rak</label>
                        <input type="number" name="rack_number" id="rack_number" min="1" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label for="row_number" class="block text-sm font-medium text-gray-700 mb-2">Nomor
                            Baris</label>
                        <input type="number" name="row_number" id="row_number" min="1" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeLocationModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .select2-container--default .select2-selection--single {
                height: 48px !important;
                /* Sesuai py-3 Tailwind (3 * 4px padding atas bawah) */
                padding: 8px 16px !important;
                /* Sesuai px-4 */
                border-radius: 0.75rem !important;
                /* rounded-xl */
                border: 1px solid #d1d5db !important;
                /* border-gray-300 */
                background-color: #ffffff !important;
                display: flex !important;
                align-items: center !important;
                box-shadow: 0 1px 2px rgb(0 0 0 / 0.05);
                /* Tailwind shadow-sm */
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                color: #374151 !important;
                /* text-gray-700 */
                font-size: 0.875rem !important;
                /* text-sm */
                line-height: 1.25rem !important;
                padding-left: 0 !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 48px !important;
                top: 0 !important;
                right: 12px !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(document).ready(function() {
                // Initialize Select2 for filter dropdowns
                $('.select2-filter').select2({
                    placeholder: function() {
                        return $(this).find('option[value=""]').text();
                    },
                    allowClear: true,
                    width: '100%'
                });


                // Handle category-classification filter dependencies
                const allClassifications = @json($classifications ?? []);

                $('#category_filter').on('change', function() {
                    const categoryId = $(this).val();
                    const classificationSelect = $('#classification_filter');

                    // Reset classification select
                    classificationSelect.empty();
                    classificationSelect.append('<option value="">Semua Klasifikasi</option>');

                    if (categoryId) {
                        // Filter classifications by selected category
                        const filteredClassifications = allClassifications.filter(c => c.category_id ==
                            categoryId);
                        filteredClassifications.forEach(function(classification) {
                            classificationSelect.append(new Option(
                                `${classification.code} - ${classification.nama_klasifikasi}`,
                                classification.id));
                        });
                    } else {
                        // Show all classifications
                        allClassifications.forEach(function(classification) {
                            classificationSelect.append(new Option(
                                `${classification.code} - ${classification.nama_klasifikasi}`,
                                classification.id));
                        });
                    }

                    // Reinitialize Select2
                    classificationSelect.select2({
                        placeholder: "Semua Klasifikasi",
                        allowClear: true,
                        width: '100%'
                    });
                });

                $('#classification_filter').on('change', function() {
                    const classificationId = $(this).val();

                    if (classificationId) {
                        // Find the selected classification and auto-select its category
                        const selectedClassification = allClassifications.find(c => c.id == classificationId);
                        if (selectedClassification && $('#category_filter').val() != selectedClassification
                            .category_id) {
                            $('#category_filter').val(selectedClassification.category_id).trigger(
                                'change.select2');
                        }
                    }
                });
            });

            function showLocationModal(archiveId, archiveNumber) {
                document.getElementById('locationForm').action = `/intern/storage/${archiveId}`;
                document.getElementById('locationModal').classList.remove('hidden');
            }

            function closeLocationModal() {
                document.getElementById('locationModal').classList.add('hidden');
            }

            // Handle form submission
            document.getElementById('locationForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message || 'Terjadi kesalahan saat menyimpan lokasi'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menyimpan lokasi'
                        });
                    });
            });

            // Show success message if exists
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 1500
                });
            @endif
        </script>
    @endpush
</x-app-layout>
