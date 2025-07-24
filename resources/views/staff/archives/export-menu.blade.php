<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-file-excel mr-2 text-green-600"></i>
            Export Data Arsip ke Excel
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <!-- Welcome Section -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-100 rounded-xl shadow-sm border border-green-200 p-6 mb-8">
                <div class="flex items-center mb-4">
                    <div class="w-16 h-16 bg-green-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-download text-white text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Export Data Arsip</h3>
                        <p class="text-gray-600 mt-1">Pilih status arsip yang ingin Anda export ke format Excel</p>
                    </div>
                </div>

                <!-- User Role Info -->
                <div class="flex items-center space-x-4 text-sm">
                    <div class="flex items-center">
                        <i class="fas fa-user-circle mr-2 text-green-600"></i>
                        <span class="font-medium text-gray-700">{{ auth()->user()->name }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-shield-alt mr-2 text-green-600"></i>
                        <span class="font-medium text-gray-700">{{ auth()->user()->getRoleDisplayName() }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-calendar mr-2 text-green-600"></i>
                        <span class="text-gray-600">{{ now()->format('d F Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Status Selection Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($statuses as $key => $title)
                    @php
                        $count = $archiveCounts[$key];
                        $colors = [
                            'all' => ['bg-blue-50', 'border-blue-200', 'text-blue-700', 'hover:bg-blue-100', 'fas fa-archive'],
                            'aktif' => ['bg-green-50', 'border-green-200', 'text-green-700', 'hover:bg-green-100', 'fas fa-folder-open'],
                            'inaktif' => ['bg-yellow-50', 'border-yellow-200', 'text-yellow-700', 'hover:bg-yellow-100', 'fas fa-folder'],
                            'permanen' => ['bg-purple-50', 'border-purple-200', 'text-purple-700', 'hover:bg-purple-100', 'fas fa-shield-alt'],
                            'musnah' => ['bg-red-50', 'border-red-200', 'text-red-700', 'hover:bg-red-100', 'fas fa-trash-alt']
                        ];
                        $color = $colors[$key];
                    @endphp

                    <a href="{{ route('staff.export-form', $key) }}"
                       class="block {{ $color[0] }} {{ $color[3] }} border-2 {{ $color[1] }} rounded-xl p-6 transition-all duration-200 transform hover:scale-105 hover:shadow-lg">

                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 {{ $color[0] }} rounded-lg flex items-center justify-center">
                                <i class="{{ $color[4] }} {{ $color[2] }} text-xl"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold {{ $color[2] }}">{{ number_format($count) }}</div>
                                <div class="text-sm text-gray-500">arsip</div>
                            </div>
                        </div>

                        <h3 class="text-lg font-semibold {{ $color[2] }} mb-2">{{ $title }}</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            @if($key === 'all')
                                Export semua data arsip dalam satu file Excel
                            @elseif($key === 'aktif')
                                Export arsip yang masih dalam masa aktif
                            @elseif($key === 'inaktif')
                                Export arsip yang sudah memasuki masa inaktif
                            @elseif($key === 'permanen')
                                Export arsip yang berstatus permanen
                            @else
                                Export arsip yang diusulkan untuk dimusnahkan
                            @endif
                        </p>

                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $color[0] }} {{ $color[2] }}">
                                <i class="fas fa-file-excel mr-1"></i>
                                Excel Format
                            </span>
                            <i class="fas fa-arrow-right {{ $color[2] }}"></i>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-bolt mr-2 text-yellow-500"></i>
                    Aksi Cepat
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Export Current Year -->
                    <form action="{{ route('staff.export.process') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="status" value="all">
                        <input type="hidden" name="year_from" value="{{ date('Y') }}">
                        <input type="hidden" name="year_to" value="{{ date('Y') }}">
                        <button type="submit" class="w-full flex items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200 transition-colors text-left">
                            <i class="fas fa-calendar-alt text-blue-600 text-xl mr-4"></i>
                            <div>
                                <div class="font-medium text-blue-900">Export Tahun {{ date('Y') }}</div>
                                <div class="text-sm text-blue-600">Semua arsip tahun ini</div>
                            </div>
                        </button>
                    </form>

                    <!-- Export Data Saya -->
                    <form action="{{ route('staff.export.process') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="status" value="all">
                        <input type="hidden" name="created_by" value="current_user">
                        <button type="submit" class="w-full flex items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg border border-green-200 transition-colors text-left">
                            <i class="fas fa-user text-green-600 text-xl mr-4"></i>
                            <div>
                                <div class="font-medium text-green-900">Export Data Saya</div>
                                <div class="text-sm text-green-600">Arsip yang saya buat</div>
                            </div>
                        </button>
                    </form>

                    <!-- Export Active Only -->
                    <form action="{{ route('staff.export.process') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="status" value="aktif">
                        <button type="submit" class="w-full flex items-center p-4 bg-emerald-50 hover:bg-emerald-100 rounded-lg border border-emerald-200 transition-colors text-left">
                            <i class="fas fa-folder-open text-emerald-600 text-xl mr-4"></i>
                            <div>
                                <div class="font-medium text-emerald-900">Export Aktif</div>
                                <div class="text-sm text-emerald-600">Hanya arsip aktif</div>
                            </div>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Help Section -->
            <div class="mt-8 bg-gray-50 rounded-xl p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-question-circle mr-2 text-blue-500"></i>
                    Panduan Export
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Format File Excel</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Format .xlsx yang kompatibel</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Header lengkap</li>
                            <li><i class="fas fa-check text-green-500 mr-2"></i>Data terstruktur dan siap cetak</li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Filter Yang Tersedia</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li><i class="fas fa-filter text-blue-500 mr-2"></i>Filter berdasarkan tahun</li>
                            <li><i class="fas fa-filter text-blue-500 mr-2"></i>Filter berdasarkan pembuat (TU & Intern)</li>
                            <li><i class="fas fa-filter text-blue-500 mr-2"></i>Filter berdasarkan status</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
