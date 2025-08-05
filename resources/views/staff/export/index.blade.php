<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-teal-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-excel text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">Export Excel Arsip</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Staff: Export data arsip ke format Excel
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('staff.archives.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Arsip
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

        <!-- Export Options -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Export All Archives -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-archive text-teal-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Semua Arsip</h3>
                        <p class="text-sm text-gray-600">Export seluruh data arsip</p>
                    </div>
                </div>
                <a href="{{ route('staff.export-form', 'all') }}"
                    class="w-full bg-teal-600 hover:bg-teal-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-center block">
                    <i class="fas fa-download mr-2"></i>Export Semua
                </a>
            </div>

            <!-- Export Active Archives -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-play-circle text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Arsip Aktif</h3>
                        <p class="text-sm text-gray-600">Export arsip dengan status aktif</p>
                    </div>
                </div>
                <a href="{{ route('staff.export-form', 'aktif') }}"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-center block">
                    <i class="fas fa-download mr-2"></i>Export Aktif
                </a>
            </div>

            <!-- Export Inactive Archives -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-pause-circle text-yellow-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Arsip Inaktif</h3>
                        <p class="text-sm text-gray-600">Export arsip dengan status inaktif</p>
                    </div>
                </div>
                <a href="{{ route('staff.export-form', 'inaktif') }}"
                    class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-center block">
                    <i class="fas fa-download mr-2"></i>Export Inaktif
                </a>
            </div>

            <!-- Export Permanent Archives -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-archive text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Arsip Permanen</h3>
                        <p class="text-sm text-gray-600">Export arsip dengan status permanen</p>
                    </div>
                </div>
                <a href="{{ route('staff.export-form', 'permanen') }}"
                    class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-center block">
                    <i class="fas fa-download mr-2"></i>Export Permanen
                </a>
            </div>

            <!-- Export Destroyed Archives -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-trash text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Arsip Musnah</h3>
                        <p class="text-sm text-gray-600">Export arsip dengan status musnah</p>
                    </div>
                </div>
                <a href="{{ route('staff.export-form', 'musnah') }}"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-center block">
                    <i class="fas fa-download mr-2"></i>Export Musnah
                </a>
            </div>

            <!-- Export Re-evaluated Archives -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-redo text-indigo-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Arsip Dinilai Kembali</h3>
                        <p class="text-sm text-gray-600">Export arsip yang dinilai kembali</p>
                    </div>
                </div>
                <a href="{{ route('staff.export-form', 'dinilai-kembali') }}"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-center block">
                    <i class="fas fa-download mr-2"></i>Export Dinilai Kembali
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
