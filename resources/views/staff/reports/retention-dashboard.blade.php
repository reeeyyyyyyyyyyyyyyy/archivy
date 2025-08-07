<x-app-layout>
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-teal-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900">
                            Dashboard Laporan Retensi Arsip
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-calendar-alt mr-1"></i>Monitoring arsip mendekati masa transisi
                            <span class="mx-2">•</span>
                            <i class="fas fa-clock mr-1"></i>Period: {{ $period }} hari ke depan
                            <span class="mx-2">•</span>
                            <i class="fas fa-sync mr-1"></i>{{ now()->format('d F Y H:i') }}
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Compliance</div>
                    <div class="flex items-center text-teal-600 font-semibold">
                        <i class="fas fa-shield-check mr-2"></i>
                        JRA Pergub 1 & 30
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Period Filter -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Filter Periode Alert
                            <span class="text-sm text-gray-500">(Active: {{ $period }} hari)</span>
                        </h3>
                        <div class="flex space-x-3">
                            <a href="{{ route('staff.reports.retention-dashboard', ['period' => 30]) }}"
                                class="px-4 py-2 text-sm font-medium rounded-md transition duration-200 {{ $period == 30 ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                30 Hari
                            </a>
                            <a href="{{ route('staff.reports.retention-dashboard', ['period' => 60]) }}"
                                class="px-4 py-2 text-sm font-medium rounded-md transition duration-200 {{ $period == 60 ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                60 Hari
                            </a>
                            <a href="{{ route('staff.reports.retention-dashboard', ['period' => 90]) }}"
                                class="px-4 py-2 text-sm font-medium rounded-md transition duration-200 {{ $period == 90 ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                90 Hari
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Total Archives -->
                <div class="bg-gradient-to-r from-teal-500 to-teal-600 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-white">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-archive text-3xl opacity-80"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium truncate">Total Arsip</dt>
                                    <dd class="text-2xl font-bold">{{ number_format($stats['total_archives']) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Approaching Inactive -->
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-white">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-clock text-3xl opacity-80"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium truncate">Menuju Inaktif</dt>
                                    <dd class="text-2xl font-bold">{{ $stats['approaching_inactive'] }}</dd>
                                    <dd class="text-xs opacity-80">{{ $period }} hari ke depan</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Approaching Final -->
                <div class="bg-gradient-to-r from-red-500 to-red-600 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-white">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-3xl opacity-80"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium truncate">Menuju Final</dt>
                                    <dd class="text-2xl font-bold">{{ $stats['approaching_final'] }}</dd>
                                    <dd class="text-xs opacity-80">{{ $period }} hari ke depan</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Distribution -->
                <div class="bg-gradient-to-r from-green-500 to-green-600 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-white">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-chart-pie text-3xl opacity-80"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium truncate">Aktif</dt>
                                    <dd class="text-lg font-bold">{{ $stats['aktif'] }}</dd>
                                    <dd class="text-xs opacity-80">
                                        In: {{ $stats['inaktif'] }} |
                                        Per: {{ $stats['permanen'] }} |
                                        Mus: {{ $stats['musnah'] }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alerts Tables -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Approaching Inactive Transition -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <i class="fas fa-arrow-right text-orange-500 mr-2"></i>
                                Transisi ke Inaktif ({{ $stats['approaching_inactive'] }})
                            </h3>
                            <span class="text-xs text-gray-500">{{ $period }} hari ke depan</span>
                        </div>

                        @if ($approachingInactive->isEmpty())
                            <p class="text-gray-500 text-center py-8">
                                <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i><br>
                                Tidak ada arsip yang akan berubah ke status Inaktif
                            </p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Arsip</th>
                                            <th
                                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Kategori</th>
                                            <th
                                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Jatuh Tempo</th>
                                            <th
                                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Sisa Hari</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($approachingInactive as $archive)
                                            @php
                                                $daysRemaining = today()->diffInDays(
                                                    $archive->transition_active_due,
                                                    false,
                                                );
                                                $urgencyClass =
                                                    $daysRemaining <= 7
                                                        ? 'bg-red-100 border-red-200'
                                                        : ($daysRemaining <= 30
                                                            ? 'bg-orange-100 border-orange-200'
                                                            : 'bg-yellow-100 border-yellow-200');
                                            @endphp
                                            <tr class="{{ $urgencyClass }}">
                                                <td class="px-3 py-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $archive->formatted_index_number }}</div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ Str::limit($archive->description, 40) }}</div>
                                                </td>
                                                <td class="px-3 py-4 text-sm text-gray-900">
                                                    {{ $archive->category->nama_kategori }}</td>
                                                <td class="px-3 py-4 text-sm text-gray-900">
                                                    {{ $archive->transition_active_due->format('d/m/Y') }}</td>
                                                <td class="px-3 py-4">
                                                    <span
                                                        class="px-2 py-1 text-xs font-semibold rounded-full {{ $daysRemaining <= 7 ? 'bg-red-200 text-red-800' : ($daysRemaining <= 30 ? 'bg-orange-200 text-orange-800' : 'bg-yellow-200 text-yellow-800') }}">
                                                        {{ $daysRemaining }} hari
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Approaching Final Transition -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <i class="fas fa-flag-checkered text-red-500 mr-2"></i>
                                Transisi Final ({{ $stats['approaching_final'] }})
                            </h3>
                            <span class="text-xs text-gray-500">{{ $period }} hari ke depan</span>
                        </div>

                        @if ($approachingFinal->isEmpty())
                            <p class="text-gray-500 text-center py-8">
                                <i class="fas fa-check-circle text-green-500 text-2xl mb-2"></i><br>
                                Tidak ada arsip yang akan berubah ke status final
                            </p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Arsip</th>
                                            <th
                                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Nasib Akhir</th>
                                            <th
                                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Jatuh Tempo</th>
                                            <th
                                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Sisa Hari</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($approachingFinal as $archive)
                                            @php
                                                $daysRemaining = today()->diffInDays(
                                                    $archive->transition_inactive_due,
                                                    false,
                                                );
                                                $urgencyClass =
                                                    $daysRemaining <= 7
                                                        ? 'bg-red-100 border-red-200'
                                                        : ($daysRemaining <= 30
                                                            ? 'bg-orange-100 border-orange-200'
                                                            : 'bg-yellow-100 border-yellow-200');
                                            @endphp
                                            <tr class="{{ $urgencyClass }}">
                                                <td class="px-3 py-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $archive->formatted_index_number }}</div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ Str::limit($archive->description, 40) }}</div>
                                                </td>
                                                <td class="px-3 py-4 text-sm text-gray-900">
                                                    <span
                                                        class="px-2 py-1 text-xs font-semibold rounded-full {{ str_starts_with($archive->classification->nasib_akhir, 'Musnah') ? 'bg-red-200 text-red-800' : 'bg-teal-200 text-teal-800' }}">
                                                        {{ $archive->classification->nasib_akhir }}
                                                    </span>
                                                </td>
                                                <td class="px-3 py-4 text-sm text-gray-900">
                                                    {{ $archive->transition_inactive_due->format('d/m/Y') }}</td>
                                                <td class="px-3 py-4">
                                                    <span
                                                        class="px-2 py-1 text-xs font-semibold rounded-full {{ $daysRemaining <= 7 ? 'bg-red-200 text-red-800' : ($daysRemaining <= 30 ? 'bg-orange-200 text-orange-800' : 'bg-yellow-200 text-yellow-800') }}">
                                                        {{ $daysRemaining }} hari
                                                    </span>
                                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
</div>

<!-- Archive Distribution Chart -->
<div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-chart-bar text-teal-500 mr-2"></i>
            Distribusi Arsip per Kategori
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach ($archivesByCategory as $category)
                @php
                    $percentage =
                        $stats['total_archives'] > 0
                            ? ($category->count / $stats['total_archives']) * 100
                            : 0;
                @endphp
                <div class="flex items-center">
                    <div class="flex-1">
                        <div class="flex justify-between text-sm">
                            <span class="font-medium text-gray-900">{{ $category->nama_kategori }}</span>
                            <span class="text-gray-500">{{ $category->count }} arsip
                                ({{ number_format($percentage, 1) }}%)</span>
                        </div>
                        <div class="mt-1 relative">
                            <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                                <div style="width: {{ $percentage }}%"
                                    class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-teal-500">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

</div>
</div>

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
</x-app-layout>
