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
                    <a href="{{ route('admin.storage.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Total Box</p>
                            <p class="text-3xl font-bold text-gray-900">{{ number_format($totalBoxes) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-box text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Box Berisi Arsip</p>
                            <p class="text-3xl font-bold text-green-600">{{ number_format($boxesWithArchives) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-archive text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Box Kosong</p>
                            <p class="text-3xl font-bold text-orange-600">{{ number_format($totalBoxes - $boxesWithArchives) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-box-open text-orange-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Generation Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-cogs mr-2 text-blue-500"></i>Pengaturan Label
                </h3>

                <form method="POST" action="{{ route('admin.storage.generate-box-labels.process') }}" class="space-y-6">
                    @csrf

                    <!-- Box Selection -->
                    <div>
                        <label for="box_numbers" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-list mr-2 text-indigo-500"></i>Pilih Box (Opsional)
                        </label>
                        <select name="box_numbers" id="box_numbers" class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4">
                            <option value="">Semua Box Berisi Arsip</option>
                            @foreach($boxes as $box)
                                <option value="{{ $box->box_number }}">Box {{ $box->box_number }} ({{ $box->archive_count }} arsip)</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Kosongkan untuk generate label semua box yang berisi arsip</p>
                    </div>

                    <!-- Format Selection -->
                    <div>
                        <label for="format" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-file mr-2 text-green-500"></i>Format Output
                        </label>
                        <select name="format" id="format" class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors py-3 px-4" disabled>
                            <option value="pdf">PDF</option>
                            {{-- <option value="word">Word (Coming Soon)</option> --}}
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-4">
                        {{-- <button type="submit" name="action" value="preview" class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                            <i class="fas fa-eye mr-2"></i>Preview
                        </button> --}}
                        <button type="submit" name="action" value="generate" class="bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                            <i class="fas fa-download mr-2"></i>Generate & Download
                        </button>
                    </div>
                </form>
            </div>

            <!-- Command Output -->
            @if(session('command_output'))
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-terminal mr-2 text-green-500"></i>Output Command
                    </h3>
                    <div class="bg-gray-900 text-green-400 p-4 rounded-lg font-mono text-sm overflow-x-auto">
                        <pre>{{ session('command_output') }}</pre>
                    </div>
                </div>
            @endif

            <!-- Label Preview -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-eye mr-2 text-blue-500"></i>Preview Format Label
                </h3>
                <div class="bg-gray-50 p-6 rounded-lg">
                    <div class="max-w-sm mx-auto border-2 border-black p-4 bg-white">
                        <div class="text-center border-b-2 border-black pb-2 mb-4">
                            <div class="font-bold text-sm">DINAS PENANAMAN MODAL DAN PTSP</div>
                            <div class="font-bold text-sm">PROVINSI JAWA TIMUR</div>
                        </div>
                        <div class="flex justify-between">
                            <div class="w-3/5 pr-4 border-r border-black">
                                <div class="mb-2">
                                    <div class="text-xs font-bold">NOMOR BERKAS</div>
                                    <div class="border-b border-black h-4"></div>
                                </div>
                                <div class="mb-2">
                                    <div class="text-xs font-bold">JUMLAH ARSIP</div>
                                    <div class="border-b border-black h-4"></div>
                                </div>
                                <div class="mb-2">
                                    <div class="text-xs font-bold">KAPASITAS</div>
                                    <div class="border-b border-black h-4"></div>
                                </div>
                            </div>
                            <div class="w-2/5 pl-4">
                                <div class="mb-2">
                                    <div class="text-xs font-bold">NO. BOKS</div>
                                    <div class="border-b border-black h-4"></div>
                                </div>
                                <div class="mb-2">
                                    <div class="text-xs font-bold">STATUS</div>
                                    <div class="border-b border-black h-4"></div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <div class="text-lg font-bold">BOX 1</div>
                        </div>
                    </div>
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
                        <p>Setiap label berisi informasi nomor berkas, jumlah arsip, kapasitas, nomor box, dan status.</p>
                    </div>
                    <div class="flex items-start space-x-2">
                        <i class="fas fa-check-circle mt-1"></i>
                        <p>Gunakan Preview untuk melihat hasil sebelum download.</p>
                    </div>
                    <div class="flex items-start space-x-2">
                        <i class="fas fa-exclamation-triangle mt-1"></i>
                        <p>Format Word akan tersedia dalam update selanjutnya.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Form submission confirmation
            document.querySelector('form').addEventListener('submit', function(e) {
                const action = e.submitter.value;

                if (action === 'generate') {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Konfirmasi Generate Label',
                        text: 'Anda yakin ingin menggenerate label box?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Generate!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                }
            });

            // Show success message if exists
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000
                });
            @endif

            // Show error message if exists
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ $errors->first() }}',
                    showConfirmButton: false,
                    timer: 3000
                });
            @endif
        </script>
    @endpush
</x-app-layout>
