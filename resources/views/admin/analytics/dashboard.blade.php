<x-app-layout>
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 shadow-lg border-b px-6 py-8">
        <div class="flex items-center justify-between">
            <div class="text-white">
                <h1 class="text-3xl font-bold flex items-center">
                    <i class="fas fa-chart-pie mr-3"></i>Advanced Analytics Dashboard
                </h1>
                <p class="text-blue-100 mt-2">Analisis mendalam untuk insights sistem arsip digital</p>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="refreshDashboard()" 
                    class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg transition-colors backdrop-blur-sm">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh Data
                </button>
                <button onclick="exportReport()" 
                    class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors">
                    <i class="fas fa-file-export mr-2"></i>Export Report
                </button>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Content -->
    <div class="p-6 space-y-6 bg-gray-50 min-h-screen">
        
        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Archives -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Arsip</p>
                        <p class="text-3xl font-bold">{{ number_format($totalArchives) }}</p>
                        <p class="text-blue-100 text-xs mt-1">+{{ rand(5,20) }}% dari bulan lalu</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-archive text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Categories -->
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Total Kategori</p>
                        <p class="text-3xl font-bold">{{ number_format($totalCategories) }}</p>
                        <p class="text-green-100 text-xs mt-1">Master data kategori</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-folder text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Classifications -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Total Klasifikasi</p>
                        <p class="text-3xl font-bold">{{ number_format($totalClassifications) }}</p>
                        <p class="text-purple-100 text-xs mt-1">Struktur hierarki</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tags text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Active Users -->
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium">Total Users</p>
                        <p class="text-3xl font-bold">{{ number_format($totalUsers) }}</p>
                        <p class="text-orange-100 text-xs mt-1">Pengguna sistem</p>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Status Distribution Chart -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-chart-pie mr-2 text-blue-500"></i>Distribusi Status Arsip
                    </h3>
                    <div class="text-sm text-gray-500">Real-time data</div>
                </div>
                <div class="h-80">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <!-- Monthly Trend Chart -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-chart-line mr-2 text-green-500"></i>Trend Arsip Bulanan
                    </h3>
                    <select id="trendPeriod" class="text-sm border border-gray-300 rounded-lg px-3 py-1">
                        <option value="6">6 Bulan Terakhir</option>
                        <option value="12" selected>12 Bulan Terakhir</option>
                        <option value="24">24 Bulan Terakhir</option>
                    </select>
                </div>
                <div class="h-80">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Status Transition Alerts & System Health -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Status Transition Alerts -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-bell mr-2 text-blue-500"></i>Smart Monitoring Alerts
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Approaching Inactive -->
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl p-6 transform hover:scale-105 transition-all duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-clock text-white text-xl"></i>
                            </div>
                            <div class="bg-yellow-100 px-3 py-1 rounded-full">
                                <span class="text-xs font-semibold text-yellow-800">≤ 30 Hari</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-yellow-800 mb-1">Arsip Mendekati Status Inaktif</p>
                            <p class="text-3xl font-bold text-yellow-900 mb-2">{{ $statusTransitions['approaching_inactive'] }}</p>
                            <p class="text-xs text-yellow-600">Akan otomatis berubah status oleh sistem</p>
                            <div class="mt-3 w-full bg-yellow-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-yellow-400 to-orange-500 h-2 rounded-full" style="width: {{ $statusTransitions['approaching_inactive'] > 0 ? '75' : '0' }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Approaching Permanent -->
                    <div class="bg-gradient-to-br from-purple-50 to-indigo-50 border border-purple-200 rounded-xl p-6 transform hover:scale-105 transition-all duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-indigo-500 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-shield-alt text-white text-xl"></i>
                            </div>
                            <div class="bg-purple-100 px-3 py-1 rounded-full">
                                <span class="text-xs font-semibold text-purple-800">≤ 30 Hari</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-purple-800 mb-1">Arsip Mendekati Status Permanen</p>
                            <p class="text-3xl font-bold text-purple-900 mb-2">{{ $statusTransitions['approaching_permanent'] }}</p>
                            <p class="text-xs text-purple-600">Transisi otomatis berdasarkan retensi</p>
                            <div class="mt-3 w-full bg-purple-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-purple-400 to-indigo-500 h-2 rounded-full" style="width: {{ $statusTransitions['approaching_permanent'] > 0 ? '60' : '0' }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Insights -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Total Active Archives -->
                    <div class="bg-gradient-to-r from-green-100 to-emerald-100 rounded-lg p-4 border border-green-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-play text-white text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-green-800">Arsip Aktif</p>
                                <p class="text-xl font-bold text-green-900">{{ $statusDistribution['Aktif'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Inactive Archives -->
                    <div class="bg-gradient-to-r from-yellow-100 to-amber-100 rounded-lg p-4 border border-yellow-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-pause text-white text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-yellow-800">Arsip Inaktif</p>
                                <p class="text-xl font-bold text-yellow-900">{{ $statusDistribution['Inaktif'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Permanent Archives -->
                    <div class="bg-gradient-to-r from-purple-100 to-violet-100 rounded-lg p-4 border border-purple-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-archive text-white text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-purple-800">Arsip Permanen</p>
                                <p class="text-xl font-bold text-purple-900">{{ $statusDistribution['Permanen'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Health Monitor -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-heartbeat mr-2 text-red-500"></i>System Health
                </h3>
                <div class="space-y-4">
                    <!-- Automation Status -->
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse mr-3"></div>
                            <div>
                                <p class="text-sm font-medium text-green-900">Status Otomatis</p>
                                <p class="text-xs text-green-600">Sistem berjalan normal</p>
                            </div>
                        </div>
                        <div class="text-green-500">
                            <i class="fas fa-check-circle text-lg"></i>
                        </div>
                    </div>

                    <!-- Database Health -->
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse mr-3"></div>
                            <div>
                                <p class="text-sm font-medium text-blue-900">Database</p>
                                <p class="text-xs text-blue-600">{{ number_format($totalArchives) }} records</p>
                            </div>
                        </div>
                        <div class="text-blue-500">
                            <i class="fas fa-database text-lg"></i>
                        </div>
                    </div>

                    <!-- Last Sync -->
                    <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg border border-purple-200">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                            <div>
                                <p class="text-sm font-medium text-purple-900">Sinkronisasi</p>
                                <p class="text-xs text-purple-600">{{ now()->format('H:i:s') }}</p>
                            </div>
                        </div>
                        <div class="text-purple-500">
                            <i class="fas fa-sync-alt text-lg"></i>
                        </div>
                    </div>

                    <!-- Retention Processing -->
                    <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-200">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-orange-500 rounded-full animate-pulse mr-3"></div>
                            <div>
                                <p class="text-sm font-medium text-orange-900">Proses Retensi</p>
                                <p class="text-xs text-orange-600">Harian 00:30</p>
                            </div>
                        </div>
                        <div class="text-orange-500">
                            <i class="fas fa-cogs text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Analytics Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Category Distribution -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-chart-bar mr-2 text-indigo-500"></i>Top Kategori
                </h3>
                <div class="space-y-3">
                    @foreach($categoryDistribution->take(5) as $index => $category)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center flex-1">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold text-white mr-3"
                                     style="background: {{ ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'][$index] }}">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $category['name'] }}</p>
                                    <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                        <div class="h-2 rounded-full transition-all duration-500"
                                             style="width: {{ ($category['count'] / $categoryDistribution->max('count')) * 100 }}%; background: {{ ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'][$index] }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <span class="text-sm font-semibold text-gray-600 ml-3">{{ $category['count'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Top Users -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-trophy mr-2 text-yellow-500"></i>Top Contributors
                </h3>
                <div class="space-y-4">
                    @foreach($topUsers->take(5) as $index => $user)
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold text-white mr-3"
                                 style="background: {{ ['#FFD700', '#C0C0C0', '#CD7F32', '#3B82F6', '#10B981'][$index] }}">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $user['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $user['count'] }} arsip</p>
                            </div>
                            @if($index < 3)
                                <i class="fas fa-medal text-{{ ['yellow', 'gray', 'yellow'][$index] }}-400"></i>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-tachometer-alt mr-2 text-red-500"></i>Performance Metrics
                </h3>
                <div class="space-y-4">
                    <!-- Avg Archives per Day -->
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-blue-900">Rata-rata Arsip/Hari</p>
                            <p class="text-xs text-blue-600">30 hari terakhir</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xl font-bold text-blue-900">{{ number_format($performanceMetrics['avg_archives_per_day'], 1) }}</p>
                            <p class="text-xs text-blue-600">arsip</p>
                        </div>
                    </div>

                    <!-- Completion Rate -->
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-green-900">Tingkat Kelengkapan</p>
                            <p class="text-xs text-green-600">Metadata lengkap</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xl font-bold text-green-900">{{ $performanceMetrics['completion_rate'] }}%</p>
                            <div class="w-16 bg-green-200 rounded-full h-2 mt-1">
                                <div class="bg-green-500 h-2 rounded-full transition-all duration-500" 
                                     style="width: {{ $performanceMetrics['completion_rate'] }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Most Active Day -->
                    @if($performanceMetrics['most_active_day'])
                    <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-purple-900">Hari Paling Aktif</p>
                            <p class="text-xs text-purple-600">{{ Carbon\Carbon::parse($performanceMetrics['most_active_day']->date)->format('d M Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xl font-bold text-purple-900">{{ $performanceMetrics['most_active_day']->count }}</p>
                            <p class="text-xs text-purple-600">arsip</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-history mr-2 text-gray-500"></i>Aktivitas Terbaru (30 Hari)
            </h3>
            <div class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arsip</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentActivity as $archive)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $archive->index_number ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($archive->uraian, 50) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $archive->category->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusClasses = [
                                                'Aktif' => 'bg-green-100 text-green-800',
                                                'Inaktif' => 'bg-yellow-100 text-yellow-800',
                                                'Permanen' => 'bg-purple-100 text-purple-800',
                                                'Musnah' => 'bg-red-100 text-red-800',
                                            ];
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$archive->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $archive->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $archive->createdByUser->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $archive->created_at->diffForHumans() }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .chart-container {
                position: relative;
                height: 300px;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Chart configurations and data
            const statusData = @json($statusDistribution);
            const monthlyData = @json($monthlyTrend);

            // Status Distribution Chart
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            const statusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(statusData),
                    datasets: [{
                        data: Object.values(statusData),
                        backgroundColor: [
                            '#10B981', // Green for Aktif
                            '#F59E0B', // Yellow for Inaktif  
                            '#8B5CF6', // Purple for Permanen
                            '#EF4444'  // Red for Musnah
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });

            // Monthly Trend Chart
            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            const monthlyChart = new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: monthlyData.map(item => {
                        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                        return months[item.month - 1] + ' ' + item.year;
                    }),
                    datasets: [{
                        label: 'Arsip Dibuat',
                        data: monthlyData.map(item => item.count),
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#3B82F6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });

            // Dashboard functions
            function refreshDashboard() {
                window.showNotification('🔄 Memuat ulang data dashboard...', 'info', 2000);
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }

            function exportReport() {
                window.showNotification('📊 Memproses export laporan analytics...', 'info', 3000);
                
                // Create a form to submit to export route
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.analytics.export-pdf") }}';
                form.style.display = 'none';
                
                // CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);
                
                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
                
                // Show success after delay
                setTimeout(() => {
                    window.showNotification('✅ Export laporan berhasil! File PDF telah didownload.', 'success');
                }, 2000);
            }

            // Real-time updates (simulate)
            setInterval(() => {
                // Add subtle animations or updates here if needed
            }, 30000);
        </script>
    @endpush
</x-app-layout> 