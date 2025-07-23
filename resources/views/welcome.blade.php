<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ARSIPIN - Sistem Arsip Pintar DPMPTSP Provinsi Jawa Timur</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        
        <!-- Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { font-family: 'Poppins', sans-serif; }
            .gradient-bg { background: linear-gradient(135deg, #1e40af 0%, #7c3aed 50%, #059669 100%); }
            .gradient-text { background: linear-gradient(135deg, #1e40af 0%, #7c3aed 50%, #059669 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
            .floating-animation { animation: float 6s ease-in-out infinite; }
            @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-15px); } }
            .logo-animation { animation: rotate 20s linear infinite; }
            @keyframes rotate { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
            .particle { animation: particle 15s infinite linear; opacity: 0.1; }
            @keyframes particle { 0% { transform: translateY(100vh) rotate(0deg); } 100% { transform: translateY(-100vh) rotate(360deg); } }
        </style>
    </head>
    <body class="antialiased">
        <!-- Background Particles -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="particle absolute w-2 h-2 bg-white rounded-full" style="left: 10%; animation-delay: -2s;"></div>
            <div class="particle absolute w-1 h-1 bg-blue-200 rounded-full" style="left: 20%; animation-delay: -4s;"></div>
            <div class="particle absolute w-3 h-3 bg-purple-200 rounded-full" style="left: 30%; animation-delay: -6s;"></div>
            <div class="particle absolute w-2 h-2 bg-green-200 rounded-full" style="left: 40%; animation-delay: -8s;"></div>
            <div class="particle absolute w-1 h-1 bg-white rounded-full" style="left: 50%; animation-delay: -10s;"></div>
            <div class="particle absolute w-2 h-2 bg-blue-200 rounded-full" style="left: 60%; animation-delay: -12s;"></div>
            <div class="particle absolute w-3 h-3 bg-purple-200 rounded-full" style="left: 70%; animation-delay: -14s;"></div>
            <div class="particle absolute w-1 h-1 bg-green-200 rounded-full" style="left: 80%; animation-delay: -16s;"></div>
            <div class="particle absolute w-2 h-2 bg-white rounded-full" style="left: 90%; animation-delay: -18s;"></div>
        </div>

        <!-- Main Content -->
        <div class="min-h-screen gradient-bg relative">
            <!-- Header with Auth -->
            <div class="absolute top-0 right-0 p-6 z-20">
                @if (Route::has('login'))
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm border border-white/30 text-white font-medium rounded-full hover:bg-white/30 transition-all duration-300">
                                <i class="fas fa-tachometer-alt mr-2"></i>
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm border border-white/30 text-white font-medium rounded-full hover:bg-white/30 transition-all duration-300">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Login
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 bg-white text-blue-600 font-semibold rounded-full hover:bg-gray-50 transition-all duration-300 shadow-lg">
                                    <i class="fas fa-user-plus mr-2"></i>
                                    Daftar
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>

            <!-- Main Hero Content -->
            <div class="flex items-center justify-center min-h-screen px-6 py-12">
                <div class="max-w-6xl mx-auto">
                    <div class="grid lg:grid-cols-2 gap-12 items-center">
                        <!-- Left Content -->
                        <div class="text-white text-center lg:text-left">
                            <!-- Logo & Branding -->
                            <div class="mb-8">
                                <div class="inline-flex items-center justify-center lg:justify-start mb-6">
                                    <div class="relative">
                                        <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mr-4 logo-animation">
                                            <i class="fas fa-archive text-white text-3xl"></i>
                                        </div>
                                        <div class="absolute -top-2 -right-2 w-6 h-6 bg-yellow-400 rounded-full flex items-center justify-center">
                                            <i class="fas fa-bolt text-blue-600 text-xs"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h1 class="text-4xl font-bold">ARSIPIN</h1>
                                        <p class="text-white/80 text-sm">Sistem Arsip Pintar</p>
                                    </div>
                                </div>
                                
                                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 border border-white/20 inline-block">
                                    <p class="text-white/90 font-medium text-lg">
                                        DPMPTSP Provinsi Jawa Timur
                                    </p>
                                </div>
                            </div>
                            
                            <h2 class="text-5xl lg:text-6xl font-bold leading-tight mb-6">
                                Kelola Arsip dengan
                                <span class="block text-yellow-300">Mudah & Otomatis</span>
                            </h2>
                            
                            <p class="text-xl text-white/90 mb-8 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                                Sistem Terpadu Manajemen Arsip Digital yang dirancang khusus untuk meningkatkan efisiensi 
                                pengelolaan dokumen di lingkungan DPMPTSP Provinsi Jawa Timur dengan otomatisasi penuh sesuai JRA.
                            </p>
                            
                            <!-- Call to Action -->
                            <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start mb-12">
                                @auth
                                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white text-blue-600 font-semibold rounded-xl hover:bg-gray-50 transition-all duration-300 shadow-lg transform hover:scale-105">
                                        <i class="fas fa-tachometer-alt mr-2"></i>
                                        Buka Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white text-blue-600 font-semibold rounded-xl hover:bg-gray-50 transition-all duration-300 shadow-lg transform hover:scale-105">
                                        <i class="fas fa-sign-in-alt mr-2"></i>
                                        Mulai Bekerja
                                    </a>
                                @endauth
                                
                                <button onclick="scrollToFeatures()" class="inline-flex items-center justify-center px-8 py-4 border-2 border-white/30 text-white font-semibold rounded-xl hover:bg-white/10 transition-all duration-300">
                                    <i class="fas fa-chevron-down mr-2"></i>
                                    Lihat Fitur
                                </button>
                            </div>
                            
                            <!-- Department Stats -->
                            <div class="grid grid-cols-3 gap-6 max-w-md mx-auto lg:mx-0">
                                <div class="text-center p-4 bg-white/10 backdrop-blur-sm rounded-xl border border-white/20">
                                    <div class="text-2xl font-bold text-yellow-300">Admin</div>
                                    <div class="text-sm text-white/80">Kepala TU</div>
                                </div>
                                <div class="text-center p-4 bg-white/10 backdrop-blur-sm rounded-xl border border-white/20">
                                    <div class="text-2xl font-bold text-yellow-300">Staff</div>
                                    <div class="text-sm text-white/80">Pegawai TU</div>
                                </div>
                                <div class="text-center p-4 bg-white/10 backdrop-blur-sm rounded-xl border border-white/20">
                                    <div class="text-2xl font-bold text-yellow-300">Magang</div>
                                    <div class="text-sm text-white/80">Mahasiswa</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Content - System Preview -->
                        <div class="relative">
                            <div class="floating-animation">
                                <!-- Main Dashboard Preview -->
                                <div class="bg-white rounded-3xl shadow-2xl p-8 transform rotate-3 hover:rotate-0 transition-transform duration-500">
                                    <!-- Browser Header -->
                                    <div class="flex items-center space-x-2 mb-6">
                                        <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                                        <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                                        <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                                        <div class="flex-1 h-2 bg-gray-100 rounded-full ml-4"></div>
                                    </div>
                                    
                                    <!-- Dashboard Content -->
                                    <div class="space-y-4">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-lg font-semibold text-gray-800">Dashboard ARSIPIN</h3>
                                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-archive text-white text-sm"></i>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-xl">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <div class="text-2xl font-bold text-blue-600">156</div>
                                                        <div class="text-sm text-blue-500">Arsip Aktif</div>
                                                    </div>
                                                    <i class="fas fa-folder-open text-blue-400 text-xl"></i>
                                                </div>
                                            </div>
                                            
                                            <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-xl">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <div class="text-2xl font-bold text-green-600">89</div>
                                                        <div class="text-sm text-green-500">Arsip Inaktif</div>
                                                    </div>
                                                    <i class="fas fa-archive text-green-400 text-xl"></i>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="h-2 bg-gray-100 rounded-full">
                                            <div class="h-2 bg-gradient-to-r from-blue-500 to-green-500 rounded-full" style="width: 75%"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Floating Elements -->
                                <div class="absolute -top-6 -left-6 bg-white rounded-xl shadow-lg p-3 transform -rotate-12">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-check text-green-600 text-xs"></i>
                                        </div>
                                        <div>
                                            <div class="text-xs font-semibold text-gray-800">Auto Update</div>
                                            <div class="text-xs text-gray-500">JRA Compliant</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="absolute -bottom-4 -right-4 bg-white rounded-xl shadow-lg p-3 transform rotate-12">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-download text-blue-600 text-xs"></i>
                                        </div>
                                        <div>
                                            <div class="text-xs font-semibold text-gray-800">Export Ready</div>
                                            <div class="text-xs text-gray-500">Excel Format</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <section id="features" class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">Fitur Unggulan ARSIPIN</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Solusi lengkap untuk pengelolaan arsip digital yang efisien dan sesuai standar JRA
                    </p>
                </div>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-2">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mb-6">
                            <i class="fas fa-robot text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Otomatis Pintar</h3>
                        <p class="text-gray-600 mb-4">
                            Status arsip berubah otomatis sesuai jadwal retensi JRA. Tidak perlu input manual lagi.
                        </p>
                        <div class="flex items-center text-blue-600 text-sm font-medium">
                            <span>Sesuai JRA Pergub</span>
                            <i class="fas fa-arrow-right ml-2"></i>
                        </div>
                    </div>
                    
                    <!-- Feature 2 -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-2">
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mb-6">
                            <i class="fas fa-search text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Pencarian Canggih</h3>
                        <p class="text-gray-600 mb-4">
                            Cari arsip dengan multiple filter berdasarkan kategori, klasifikasi, tanggal, dan pembuat.
                        </p>
                        <div class="flex items-center text-purple-600 text-sm font-medium">
                            <span>Temukan cepat</span>
                            <i class="fas fa-arrow-right ml-2"></i>
                        </div>
                    </div>
                    
                    <!-- Feature 3 -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-2">
                        <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mb-6">
                            <i class="fas fa-tasks text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Operasi Massal</h3>
                        <p class="text-gray-600 mb-4">
                            Update ratusan arsip sekaligus dengan bulk edit, delete, dan export untuk efisiensi maksimal.
                        </p>
                        <div class="flex items-center text-green-600 text-sm font-medium">
                            <span>Hemat waktu</span>
                            <i class="fas fa-arrow-right ml-2"></i>
                        </div>
                    </div>
                    
                    <!-- Feature 4 -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-2">
                        <div class="w-14 h-14 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center mb-6">
                            <i class="fas fa-file-excel text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Export Profesional</h3>
                        <p class="text-gray-600 mb-4">
                            Format Excel yang rapi dan terstruktur, siap untuk laporan resmi dan kebutuhan audit.
                        </p>
                        <div class="flex items-center text-orange-600 text-sm font-medium">
                            <span>Siap audit</span>
                            <i class="fas fa-arrow-right ml-2"></i>
                        </div>
                    </div>
                    
                    <!-- Feature 5 -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-2">
                        <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-pink-500 rounded-xl flex items-center justify-center mb-6">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Laporan Retensi</h3>
                        <p class="text-gray-600 mb-4">
                            Dashboard analytics dengan prediksi arsip yang akan jatuh tempo untuk perencanaan yang lebih baik.
                        </p>
                        <div class="flex items-center text-red-600 text-sm font-medium">
                            <span>Prediksi akurat</span>
                            <i class="fas fa-arrow-right ml-2"></i>
                        </div>
                    </div>
                    
                    <!-- Feature 6 -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-2">
                        <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center mb-6">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Multi-User Role</h3>
                        <p class="text-gray-600 mb-4">
                            Akses bertingkat untuk Admin, Pegawai TU, dan Mahasiswa Magang dengan hak akses yang berbeda.
                        </p>
                        <div class="flex items-center text-indigo-600 text-sm font-medium">
                            <span>Aman terkontrol</span>
                            <i class="fas fa-arrow-right ml-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center">
                    <div class="flex items-center justify-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-archive text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold">ARSIPIN</h3>
                            <p class="text-gray-400 text-sm">Sistem Arsip Pintar</p>
                        </div>
                    </div>
                    
                    <p class="text-gray-400 mb-6 max-w-2xl mx-auto">
                        Sistem Terpadu Manajemen Arsip Digital<br>
                        DPMPTSP Provinsi Jawa Timur
                    </p>
                    
                    <div class="border-t border-gray-800 pt-6">
                        <p class="text-gray-500 text-sm">
                            &copy; {{ date('Y') }} ARSIPIN - DPMPTSP Provinsi Jawa Timur. Semua hak dilindungi.
                        </p>
                    </div>
                </div>
            </div>
        </footer>

        <!-- JavaScript -->
        <script>
            function scrollToFeatures() {
                document.getElementById('features').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        </script>
    </body>
</html>
