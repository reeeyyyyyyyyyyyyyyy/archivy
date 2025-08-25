<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARSIPIN - Sistem Arsip Digital DPMPTSP Provinsi Jawa Timur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary-blue: #2563eb;
            --primary-indigo: #4f46e5;
            --primary-purple: #7c3aed;
            --accent-emerald: #10b981;
            --accent-amber: #f59e0b;
            --accent-rose: #f43f5e;
            --dark-slate: #0f172a;
            --slate-800: #1e293b;
            --slate-700: #334155;
            --slate-600: #475569;
            --slate-100: #f1f5f9;
            --white: #ffffff;
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            color: var(--white);
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* Animated Background */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .floating-shape {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
            opacity: 0.1;
            animation: float 20s infinite linear;
        }

        .floating-shape:nth-child(1) {
            width: 300px;
            height: 300px;
            top: 10%;
            left: 5%;
            animation-delay: 0s;
        }

        .floating-shape:nth-child(2) {
            width: 200px;
            height: 200px;
            top: 60%;
            right: 10%;
            animation-delay: 10s;
        }

        .floating-shape:nth-child(3) {
            width: 150px;
            height: 150px;
            bottom: 20%;
            left: 20%;
            animation-delay: 5s;
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 2rem;
            position: relative;
            z-index: 10;
        }

        .hero-container {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            text-align: center;
        }

        .hero-logo {
            margin-bottom: 2rem;
            animation: fadeInDown 1s ease-out;
        }

        .logo-icon {
            display: inline-block;
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
            padding: 2rem;
            border-radius: 24px;
            font-size: 4rem;
            color: var(--white);
            margin-bottom: 1rem;
            box-shadow: 0 20px 40px rgba(37, 99, 235, 0.3);
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, var(--white), var(--primary-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: fadeInUp 1s ease-out 0.3s both;
        }

        .hero-subtitle {
            font-size: 1.5rem;
            color: var(--slate-100);
            margin-bottom: 3rem;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            animation: fadeInUp 1s ease-out 0.6s both;
        }

        .hero-description {
            font-size: 1.125rem;
            color: var(--slate-100);
            margin-bottom: 3rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.7;
            animation: fadeInUp 1s ease-out 0.9s both;
        }

        .cta-buttons {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 4rem;
            animation: fadeInUp 1s ease-out 1.2s both;
        }

        .btn {
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.125rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
            color: var(--white);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(37, 99, 235, 0.4);
        }

        .btn-outline {
            background: transparent;
            color: var(--white);
            border: 2px solid var(--primary-blue);
        }

        .btn-outline:hover {
            background: var(--primary-blue);
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(37, 99, 235, 0.3);
        }

        /* Stats Section */
        .hero-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            max-width: 800px;
            margin: 0 auto 4rem;
            animation: fadeInUp 1s ease-out 1.5s both;
        }

        .stat-item {
            text-align: center;
            padding: 2rem;
            background: var(--glass-bg);
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(20px);
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-5px);
            border-color: var(--primary-blue);
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .stat-number {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-blue);
            margin-bottom: 0.75rem;
        }

        .stat-label {
            color: var(--slate-100);
            font-size: 0.9rem;
            font-weight: 500;
            line-height: 1.4;
        }

        /* Features Section */
        .features {
            padding: 6rem 2rem;
            background: var(--slate-800);
        }

        .section-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--white);
        }

        .section-subtitle {
            font-size: 1.25rem;
            color: var(--slate-100);
            max-width: 700px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: var(--slate-700);
            padding: 2.5rem;
            border-radius: 20px;
            border: 1px solid var(--slate-600);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-blue), var(--primary-purple));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            border-color: var(--primary-blue);
        }

        .feature-icon {
            font-size: 3rem;
            color: var(--primary-blue);
            margin-bottom: 1.5rem;
        }

        .feature-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--white);
        }

        .feature-desc {
            color: var(--slate-100);
            line-height: 1.7;
            font-size: 1.125rem;
        }

        /* Internal Notice */
        .internal-notice {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
            padding: 3rem 2rem;
            text-align: center;
            margin: 4rem 0;
        }

        .internal-notice h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--white);
        }

        .internal-notice p {
            font-size: 1.125rem;
            color: rgba(255, 255, 255, 0.9);
            max-width: 600px;
            margin: 0 auto;
        }

        /* Footer */
        .footer {
            background: var(--dark-slate);
            padding: 3rem 2rem 1rem;
            border-top: 1px solid var(--slate-700);
            text-align: center;
        }

        .footer-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .footer-logo-icon {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
            padding: 1rem;
            border-radius: 16px;
            font-size: 1.5rem;
            color: var(--white);
        }

        .footer-logo-text h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--white);
        }

        .footer-logo-text p {
            color: var(--slate-100);
            font-size: 0.875rem;
        }

        .footer-info {
            color: var(--slate-100);
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .footer-bottom {
            padding-top: 2rem;
            border-top: 1px solid var(--slate-700);
            color: var(--slate-100);
        }

        /* Animations */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            33% {
                transform: translateY(-30px) rotate(120deg);
            }

            66% {
                transform: translateY(15px) rotate(240deg);
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.25rem;
            }

            .hero-stats {
                grid-template-columns: 1fr;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--slate-800);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-blue);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-purple);
        }
    </style>
</head>

<body>
    <!-- Animated Background -->
    <div class="animated-bg">
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
    </div>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-container">
            <div class="hero-logo">
                <div class="logo-icon">
                    <i class="fas fa-archive"></i>
                </div>
            </div>

            <h1 class="hero-title">ARSIPIN</h1>
            <p class="hero-subtitle">Sistem Arsip Digital Terdepan</p>
                <p class="hero-description">
                Sistem arsip digital yang dibuat khusus untuk DPMPTSP Provinsi Jawa Timur.
                Kelola dokumen arsip dengan mudah, aman, dan sesuai dengan aturan yang berlaku.
                </p>

                <div class="cta-buttons">
                <a href="{{ route('login') }}" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i>
                    Masuk ke Sistem
                    </a>
                <a href="{{ route('register') }}" class="btn btn-outline">
                    <i class="fas fa-user-plus"></i>
                    Daftar Akun
                    </a>
                </div>

            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-user-shield text-blue-600"></i>
                    </div>
                    <div class="stat-number">Admin</div>
                    <div class="stat-label">Kelola Sistem & Master Data</div>
                    </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-user-tie text-emerald-600"></i>
                    </div>
                    <div class="stat-number">Staff</div>
                    <div class="stat-label">Kelola Arsip & Laporan</div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-user-graduate text-orange-600"></i>
            </div>
                    <div class="stat-number">Intern</div>
                    <div class="stat-label">Bantu Kelola Arsip</div>
                            </div>
                            </div>
                            </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="section-container">
            <div class="section-header" data-aos="fade-up">
                <h2 class="section-title">Apa yang Bisa Anda Lakukan?</h2>
                <p class="section-subtitle">Fitur-fitur yang tersedia untuk memudahkan pengelolaan arsip di DPMPTSP
                </p>
                        </div>

            <div class="features-grid">
                <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-icon">
                        <i class="fas fa-archive"></i>
                        </div>
                    <h3 class="feature-title">Kelola Arsip</h3>
                    <p class="feature-desc">Buat arsip baru, lihat semua arsip, dan kelola arsip terkait dengan mudah. Sistem kategori dan klasifikasi yang terstruktur.</p>
                    </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon">
                        <i class="fas fa-search"></i>
                            </div>
                    <h3 class="feature-title">Cari Arsip</h3>
                    <p class="feature-desc">Temukan arsip dengan cepat. Pencarian tidak membedakan huruf besar atau kecil, sehingga lebih mudah menemukan arsip yang dicari.</p>
                        </div>

                {{-- <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-icon">
                        <i class="fas fa-tasks"></i>
        </div>
                    <h3 class="feature-title">Operasi Massal</h3>
                    <p class="feature-desc">Ubah status, pindah lokasi, dan hapus arsip secara bersamaan. Hemat waktu dengan mengelola banyak arsip sekaligus.</p>
                </div> --}}

                <div class="feature-card" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-icon">
                        <i class="fas fa-database"></i>
                </div>
                    <h3 class="feature-title">Atur Lokasi</h3>
                    <p class="feature-desc">Tentukan lokasi penyimpanan arsip dengan sistem rak, baris, dan box. Lihat kapasitas dan atur lokasi dengan mudah.</p>
            </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="500">
                <div class="feature-icon">
                    <i class="fas fa-link"></i>
                </div>
                    <h3 class="feature-title">Arsip Terkait</h3>
                    <p class="feature-desc">Sistem arsip terkait yang memudahkan pengelolaan arsip yang terkait dengan arsip lainnya.</p>
            </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="600">
                <div class="feature-icon">
                        <i class="fas fa-file-excel"></i>
                </div>
                    <h3 class="feature-title">Export Excel</h3>
                    <p class="feature-desc">Download data arsip ke format Excel. Pilih data berdasarkan status, kategori, atau tanggal yang diinginkan.</p>
            </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="700">
                <div class="feature-icon">
                        <i class="fas fa-tags"></i>
                </div>
                    <h3 class="feature-title">Buat Label Box</h3>
                    <p class="feature-desc">Buat label untuk box arsip secara otomatis. Label berisi informasi lokasi dan isi box untuk memudahkan pencarian.</p>
            </div>

                {{-- <div class="feature-card" data-aos="fade-up" data-aos-delay="800">
                <div class="feature-icon">
                        <i class="fas fa-redo"></i>
                </div>
                    <h3 class="feature-title">Arsip Dinilai Kembali</h3>
                    <p class="feature-desc">Kelola arsip yang perlu penilaian ulang. Ubah status arsip dan tambah catatan evaluasi dengan mudah.</p>
            </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="900">
                <div class="feature-icon">
                        <i class="fas fa-users-cog"></i>
                </div>
                    <h3 class="feature-title">Kelola Pengguna</h3>
                    <p class="feature-desc">Atur hak akses pengguna berdasarkan role. Admin dapat mengelola kategori, klasifikasi, dan role pengguna.</p>
                </div> --}}
            </div>
        </div>
    </section>

    <!-- Internal Notice -->
    <section class="internal-notice">
        <h2>Khusus Pengguna Internal</h2>
        <p>Sistem ini dirancang khusus untuk pegawai DPMPTSP Provinsi Jawa Timur. Akses terbatas dan aman untuk
            pengelolaan arsip internal.</p>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
                <div class="footer-logo">
                    <div class="footer-logo-icon">
                        <i class="fas fa-archive"></i>
                    </div>
                    <div class="footer-logo-text">
                        <h3>ARSIPIN</h3>
                    <p>Sistem Arsip Digital</p>
                    </div>
                </div>

            <div class="footer-info">
                <p>
                    <strong>DPMPTSP Provinsi Jawa Timur</strong><br>
                    Jl. Pahlawan No.116, Krembangan Sel., Kec. Krembangan, Surabaya, Jawa Timur 60175<br>
                </p>
            </div>
            </div>

        <div class="footer-bottom">
            <p>&copy; 2025 ARSIPIN - DPMPTSP Provinsi Jawa Timur. Semua hak cipta dilindungi.</p>
        </div>
    </footer>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add parallax effect to floating shapes
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const shapes = document.querySelectorAll('.floating-shape');

            shapes.forEach((shape, index) => {
                const speed = 0.5 + (index * 0.1);
                shape.style.transform = `translateY(${scrolled * speed}px) rotate(${scrolled * 0.1}deg)`;
            });
        });
    </script>
</body>

</html>
