<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-teal-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-tags text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Generate Label Box</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Staff: Generate label untuk box penyimpanan arsip
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('staff.storage.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Storage
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-6 space-y-6">
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Generate Label Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-cogs mr-2 text-teal-500"></i>Generate Label Box
            </h3>

            <form method="POST" action="{{ route('staff.generate-labels.generate') }}" class="space-y-6">
                @csrf

                <!-- Rack Selection -->
                <div>
                    <label for="rack_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-warehouse mr-2 text-teal-500"></i>Pilih Rak
                    </label>
                    <select name="rack_id" id="rack_id"
                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors py-3 px-4"
                        required>
                        <option value="">Pilih Rak...</option>
                        @foreach ($racks as $rack)
                            <option value="{{ $rack->id }}">
                                {{ $rack->name }} ({{ $rack->getAvailableBoxesCount() }} box tersedia)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Box Range -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="box_start" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-sort-numeric-up mr-2 text-teal-500"></i>Box Awal
                        </label>
                        <input type="number" name="box_start" id="box_start" min="1"
                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors py-3 px-4"
                            required>
                    </div>
                    <div>
                        <label for="box_end" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-sort-numeric-down mr-2 text-teal-500"></i>Box Akhir
                        </label>
                        <input type="number" name="box_end" id="box_end" min="1"
                            class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors py-3 px-4"
                            required>
                    </div>
                </div>

                <!-- Output Format -->
                <div>
                    <label for="format" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-file mr-2 text-teal-500"></i>Format Output
                    </label>
                    <select name="format" id="format"
                        class="w-full bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors py-3 px-4"
                        required>
                        <option value="pdf">PDF</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" onclick="previewLabels()"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-eye mr-2"></i>Preview
                    </button>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-download mr-2"></i>Generate Label
                    </button>
                </div>
            </form>
        </div>

        <!-- Preview Section -->
        <div id="previewSection" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hidden">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-eye mr-2 text-teal-500"></i>Preview Label
            </h3>
            <div id="previewContent" class="space-y-4">
                <!-- Preview content will be loaded here -->
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function previewLabels() {
                const rackId = document.getElementById('rack_id').value;
                const boxStart = document.getElementById('box_start').value;
                const boxEnd = document.getElementById('box_end').value;

                if (!rackId || !boxStart || !boxEnd) {
                    alert('Silakan isi semua field yang diperlukan');
                    return;
                }

                // Show loading
                document.getElementById('previewSection').classList.remove('hidden');
                document.getElementById('previewContent').innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-teal-600 text-xl"></i><p class="mt-2 text-gray-600">Loading preview...</p></div>';

                // Fetch preview data
                fetch(`{{ route('staff.generate-labels.preview') }}?rack_id=${rackId}&box_start=${boxStart}&box_end=${boxEnd}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('previewContent').innerHTML = data.html;
                        } else {
                            document.getElementById('previewContent').innerHTML = '<div class="text-center py-4 text-red-600"><i class="fas fa-exclamation-triangle text-xl"></i><p class="mt-2">Error: ' + data.message + '</p></div>';
                        }
                    })
                    .catch(error => {
                        document.getElementById('previewContent').innerHTML = '<div class="text-center py-4 text-red-600"><i class="fas fa-exclamation-triangle text-xl"></i><p class="mt-2">Error loading preview</p></div>';
                    });
            }
        </script>
    @endpush
</x-app-layout>
