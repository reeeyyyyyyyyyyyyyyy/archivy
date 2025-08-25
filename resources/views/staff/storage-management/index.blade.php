<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-teal-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-warehouse text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Manajemen Lokasi Arsip</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Kelola rak, baris, dan kapasitas penyimpanan arsip
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('staff.storage-management.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>Tambah Rak
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

        <!-- Racks Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($racks as $rack)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $rack->name }}</h3>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                            {{ $rack->status === 'active' ? 'bg-green-100 text-green-800' :
                               ($rack->status === 'inactive' ? 'bg-gray-100 text-gray-800' :
                               'bg-red-100 text-red-800') }}">
                            {{ ucfirst($rack->status) }}
                        </span>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Total Baris:</span>
                            <span class="font-semibold">{{ $rack->total_rows }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Total Box:</span>
                            <span class="font-semibold">{{ $rack->total_boxes }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Kapasitas per Box:</span>
                            <span class="font-semibold">{{ $rack->capacity_per_box }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Box Tersedia:</span>
                            <span class="font-semibold text-green-600">{{ $rack->getAvailableBoxesCount() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Utilisasi:</span>
                            <span class="font-semibold">{{ $rack->getUtilizationPercentage() }}%</span>
                        </div>
                    </div>

                    @if($rack->description)
                        <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-600">{{ $rack->description }}</p>
                        </div>
                    @endif

                    <!-- Progress Bar -->
                    <div class="mt-4">
                        <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                            <span>Kapasitas</span>
                            <span>{{ $rack->getUtilizationPercentage() }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-teal-600 h-2 rounded-full transition-all duration-300"
                                style="width: {{ $rack->getUtilizationPercentage() }}%"></div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200">
                        <a href="{{ route('staff.storage-management.show', $rack) }}"
                            class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-1K00 text-white text-xs rounded-lg transition-colors">
                            <i class="fas fa-eye mr-1"></i>Detail
                        </a>
                        <a href="{{ route('staff.storage-management.edit', $rack) }}"
                            class="inline-flex items-center px-3 py-1 bg-teal-600 hover:bg-teal-700 text-white text-xs rounded-lg transition-colors">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        @if($racks->isEmpty())
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-warehouse text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada rak</h3>
                <p class="text-gray-600 mb-6">Mulai dengan membuat rak pertama untuk penyimpanan arsip</p>
                <a href="{{ route('staff.storage-management.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>Tambah Rak Pertama
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
