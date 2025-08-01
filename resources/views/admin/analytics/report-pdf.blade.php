<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Analytics Sistem Arsip Digital</title>
    <style>
        @page {
            margin: 20mm;
            @top-center {
                content: "LAPORAN ANALYTICS SISTEM ARSIP DIGITAL";
                font-size: 10px;
                color: #666;
            }
            @bottom-center {
                content: "Halaman " counter(page) " dari " counter(pages);
                font-size: 10px;
                color: #666;
            }
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
            color: #1e40af;
            text-transform: uppercase;
        }
        
        .header h2 {
            font-size: 16px;
            margin: 5px 0;
            color: #374151;
        }
        
        .header h3 {
            font-size: 14px;
            margin: 5px 0;
            color: #6b7280;
        }
        
        .header .meta {
            margin-top: 15px;
            font-size: 10px;
            color: #6b7280;
        }
        
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 15px;
            padding: 8px 12px;
            background: #f1f5f9;
            border-left: 4px solid #2563eb;
        }
        
        .grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .grid-2 {
            display: table-cell;
            width: 50%;
            padding-right: 10px;
            vertical-align: top;
        }
        
        .grid-3 {
            display: table-cell;
            width: 33.33%;
            padding-right: 10px;
            vertical-align: top;
        }
        
        .grid-4 {
            display: table-cell;
            width: 25%;
            padding-right: 10px;
            vertical-align: top;
        }
        
        .metric-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .metric-card .number {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }
        
        .metric-card .label {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .table th {
            background: #1e40af;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
        }
        
        .table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }
        
        .table tr:nth-child(even) {
            background: #f8fafc;
        }
        
        .status-aktif { color: #059669; font-weight: bold; }
        .status-inaktif { color: #d97706; font-weight: bold; }
        .status-permanen { color: #7c3aed; font-weight: bold; }
        .status-musnah { color: #dc2626; font-weight: bold; }
        
        .alert-box {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 10px;
        }
        
        .alert-box.success {
            background: #d1fae5;
            border-color: #10b981;
        }
        
        .progress-bar {
            background: #e5e7eb;
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 5px;
        }
        
        .progress-fill {
            height: 100%;
            background: #2563eb;
            transition: width 0.3s ease;
        }
        
        .signature-area {
            margin-top: 40px;
            text-align: right;
        }
        
        .signature-box {
            display: inline-block;
            text-align: center;
            margin-left: 40px;
        }
        
        .signature-line {
            width: 200px;
            border-bottom: 1px solid #333;
            margin: 50px auto 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Laporan Analytics Sistem Arsip Digital</h1>
        <h2>DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU</h2>
        <h3>PROVINSI JAWA TIMUR</h3>
        <div class="meta">
            <strong>Periode:</strong> {{ $period }} | 
            <strong>Tanggal Generate:</strong> {{ $generated_at->format('d F Y, H:i:s') }} | 
            <strong>Sistem:</strong> ARSIPIN v2.0
        </div>
    </div>

    <!-- Executive Summary -->
    <div class="section">
        <div class="section-title">RINGKASAN EKSEKUTIF</div>
        <div class="grid">
            <div class="grid-4">
                <div class="metric-card">
                    <div class="number">{{ number_format($total_archives) }}</div>
                    <div class="label">Total Arsip</div>
                </div>
            </div>
            <div class="grid-4">
                <div class="metric-card">
                    <div class="number">{{ number_format($total_categories) }}</div>
                    <div class="label">Total Kategori</div>
                </div>
            </div>
            <div class="grid-4">
                <div class="metric-card">
                    <div class="number">{{ number_format($total_classifications) }}</div>
                    <div class="label">Total Klasifikasi</div>
                </div>
            </div>
            <div class="grid-4">
                <div class="metric-card">
                    <div class="number">{{ number_format($total_users) }}</div>
                    <div class="label">Total Pengguna</div>
                </div>
            </div>
        </div>
        
        <p style="text-align: justify; margin-top: 20px;">
            Sistem Arsip Digital ARSIPIN telah berhasil mengelola <strong>{{ number_format($total_archives) }} arsip</strong> 
            dengan tingkat kelengkapan data <strong>{{ $performance_metrics['completion_rate'] }}%</strong>. 
            Sistem automasi berjalan dengan baik dan telah memproses transisi status arsip sesuai dengan 
            jadwal retensi yang ditetapkan berdasarkan JRA Pergub 1 & 30 Jawa Timur.
        </p>
    </div>

    <!-- Status Distribution -->
    <div class="section">
        <div class="section-title">DISTRIBUSI STATUS ARSIP</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Status Arsip</th>
                    <th>Jumlah</th>
                    <th>Persentase</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($status_distribution as $status)
                    @php
                        $percentage = $total_archives > 0 ? round(($status->count / $total_archives) * 100, 2) : 0;
                        $statusClass = match($status->status) {
                            'Aktif' => 'status-aktif',
                            'Inaktif' => 'status-inaktif', 
                            'Permanen' => 'status-permanen',
                            'Musnah' => 'status-musnah',
                            default => ''
                        };
                        $description = match($status->status) {
                            'Aktif' => 'Arsip dalam periode aktif sesuai retensi',
                            'Inaktif' => 'Arsip telah melewati masa aktif, dalam periode inaktif',
                            'Permanen' => 'Arsip dengan nilai permanen untuk disimpan selamanya',
                            'Musnah' => 'Arsip yang telah diusulkan untuk dimusnahkan',
                            default => 'Status tidak dikenal'
                        };
                    @endphp
                    <tr>
                        <td class="{{ $statusClass }}">{{ $status->status }}</td>
                        <td>{{ number_format($status->count) }}</td>
                        <td>{{ $percentage }}%</td>
                        <td>{{ $description }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Category Analysis -->
    <div class="section">
        <div class="section-title">ANALISIS DISTRIBUSI KATEGORI</div>
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kategori</th>
                    <th>Kode</th>
                    <th>Jumlah Arsip</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                @foreach($category_distribution->take(15) as $index => $category)
                    @php
                        $percentage = $total_archives > 0 ? round(($category->archives_count / $total_archives) * 100, 2) : 0;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->code }}</td>
                        <td>{{ number_format($category->archives_count) }}</td>
                        <td>{{ $percentage }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Status Transitions & Automation -->
    <div class="section">
        <div class="section-title">MONITORING TRANSISI STATUS OTOMATIS</div>
        <div class="grid">
            <div class="grid-2">
                <div class="alert-box">
                    <strong>Mendekati Status Inaktif (30 hari ke depan)</strong><br>
                    <span style="font-size: 20px; color: #d97706;">{{ number_format($status_transitions['approaching_inactive']) }}</span> arsip
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $status_transitions['approaching_inactive'] > 0 ? '75' : '0' }}%; background: #d97706;"></div>
                    </div>
                </div>
            </div>
            <div class="grid-2">
                <div class="alert-box">
                    <strong>Mendekati Status Permanen (30 hari ke depan)</strong><br>
                    <span style="font-size: 20px; color: #7c3aed;">{{ number_format($status_transitions['approaching_permanent']) }}</span> arsip
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $status_transitions['approaching_permanent'] > 0 ? '60' : '0' }}%; background: #7c3aed;"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="alert-box success">
            <strong>Status Sistem Automasi:</strong> AKTIF ✓ <br>
            Sistem automasi berjalan setiap hari pada pukul 00:30 WIB untuk memproses transisi status arsip 
            berdasarkan jadwal retensi yang telah ditetapkan. Tidak diperlukan intervensi manual.
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="section page-break">
        <div class="section-title">METRIK KINERJA SISTEM</div>
        <div class="grid">
            <div class="grid-3">
                <div class="metric-card">
                    <div class="number">{{ number_format($performance_metrics['avg_archives_per_day'], 1) }}</div>
                    <div class="label">Rata-rata Arsip/Hari</div>
                    <small>(30 hari terakhir)</small>
                </div>
            </div>
            <div class="grid-3">
                <div class="metric-card">
                    <div class="number">{{ $performance_metrics['completion_rate'] }}%</div>
                    <div class="label">Tingkat Kelengkapan Data</div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $performance_metrics['completion_rate'] }}%;"></div>
                    </div>
                </div>
            </div>
            <div class="grid-3">
                <div class="metric-card">
                    <div class="number">
                        @if($performance_metrics['most_active_month'])
                            {{ $performance_metrics['most_active_month']->count }}
                        @else
                            0
                        @endif
                    </div>
                    <div class="label">Bulan Paling Aktif</div>
                    <small>
                        @if($performance_metrics['most_active_month'])
                            {{ $performance_metrics['most_active_month']->month }}
                        @else
                            -
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Trends -->
    <div class="section">
        <div class="section-title">TREND PEMBUATAN ARSIP (12 BULAN TERAKHIR)</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Tahun</th>
                    <th>Jumlah Arsip Dibuat</th>
                    <th>Persentase dari Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthly_trends as $trend)
                    @php
                        $monthNames = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                        $percentage = $total_archives > 0 ? round(($trend->count / $total_archives) * 100, 2) : 0;
                    @endphp
                    <tr>
                        <td>{{ $monthNames[$trend->month] }}</td>
                        <td>{{ $trend->year }}</td>
                        <td>{{ number_format($trend->count) }}</td>
                        <td>{{ $percentage }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- System Information -->
    <div class="section">
        <div class="section-title">INFORMASI SISTEM</div>
        <table class="table">
            <tr>
                <td><strong>Nama Sistem</strong></td>
                <td>ARSIPIN (Arsip Pintar) - Sistem Arsip Digital</td>
            </tr>
            <tr>
                <td><strong>Versi</strong></td>
                <td>2.0 - Advanced Analytics</td>
            </tr>
            <tr>
                <td><strong>Total Records Database</strong></td>
                <td>{{ number_format($system_health['database_size']) }} arsip</td>
            </tr>
            <tr>
                <td><strong>Arsip Terbaru</strong></td>
                <td>
                    @if($system_health['last_archive'])
                        {{ $system_health['last_archive']->created_at->format('d F Y H:i') }}
                        ({{ $system_health['last_archive']->index_number ?? 'N/A' }})
                    @else
                        Belum ada arsip
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong>Arsip Pertama</strong></td>
                <td>
                    @if($system_health['oldest_archive'])
                        {{ $system_health['oldest_archive']->created_at->format('d F Y H:i') }}
                        ({{ $system_health['oldest_archive']->index_number ?? 'N/A' }})
                    @else
                        Belum ada arsip
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong>Compliance</strong></td>
                <td>JRA Pergub 1 & 30 Provinsi Jawa Timur</td>
            </tr>
        </table>
    </div>

    <!-- Conclusion -->
    <div class="section">
        <div class="section-title">KESIMPULAN DAN REKOMENDASI</div>
        <div style="text-align: justify;">
            <p><strong>Kesimpulan:</strong></p>
            <ul>
                <li>Sistem ARSIPIN telah berhasil mengelola {{ number_format($total_archives) }} arsip dengan tingkat kelengkapan data {{ $performance_metrics['completion_rate'] }}%</li>
                <li>Automasi transisi status arsip berjalan dengan baik sesuai jadwal retensi yang ditetapkan</li>
                <li>Distribusi arsip per kategori menunjukkan pola yang seimbang sesuai dengan aktivitas operasional instansi</li>
                <li>Sistem monitoring memberikan early warning untuk arsip yang akan mengalami transisi status</li>
            </ul>
            
            <p><strong>Rekomendasi:</strong></p>
            <ul>
                <li>Lanjutkan monitoring rutin terhadap arsip yang mendekati periode transisi status</li>
                <li>Pertahankan tingkat kelengkapan metadata arsip di atas 90%</li>
                <li>Lakukan backup data secara berkala untuk menjaga integritas arsip digital</li>
                <li>Evaluasi berkala terhadap kategori dan klasifikasi untuk optimalisasi struktur arsip</li>
            </ul>
        </div>
    </div>

    <!-- Signature -->
    <div class="signature-area">
        <div style="text-align: center;">
            Surabaya, {{ $generated_at->format('d F Y') }}
            <br><br>
            <strong>KEPALA DINAS PENANAMAN MODAL DAN</strong><br>
            <strong>PELAYANAN TERPADU SATU PINTU</strong><br>
            <strong>PROVINSI JAWA TIMUR</strong>
            <div class="signature-line"></div>
            <strong>Nama Pejabat</strong><br>
            NIP. XXXX XXXX XXXX XXXX
        </div>
    </div>
</body>
</html> 