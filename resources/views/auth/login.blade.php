<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - ARSIPIN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        /* Background Animation */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 20% 80%, rgba(37, 99, 235, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(124, 58, 237, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(16, 185, 129, 0.05) 0%, transparent 50%);
            z-index: -1;
        }

        /* Floating Elements */
        .floating-element {
            position: fixed;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
            opacity: 0.1;
            animation: float 20s infinite linear;
        }

        .floating-element:nth-child(1) {
            width: 200px;
            height: 200px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-element:nth-child(2) {
            width: 150px;
            height: 150px;
            top: 60%;
            right: 10%;
            animation-delay: 10s;
        }

        .floating-element:nth-child(3) {
            width: 100px;
            height: 100px;
            bottom: 20%;
            left: 20%;
            animation-delay: 5s;
        }

        /* Main Container */
        .auth-container {
            width: 100%;
            max-width: 1200px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            position: relative;
            z-index: 10;
        }

        /* Left Side - Info */
        .auth-info {
            padding: 2rem;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 3rem;
        }

        .logo-icon {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
            padding: 1rem;
            border-radius: 16px;
            font-size: 2rem;
            color: var(--white);
        }

        .logo-text h1 {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--white), var(--primary-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .logo-text p {
            color: var(--slate-100);
            font-size: 0.875rem;
        }

        .welcome-text h2 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            line-height: 1.1;
        }

        .welcome-text p {
            font-size: 1.125rem;
            color: var(--slate-100);
            line-height: 1.7;
            margin-bottom: 2rem;
        }

        .features-list {
            list-style: none;
            margin-bottom: 2rem;
        }

        .features-list li {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            color: var(--slate-100);
        }

        .feature-icon {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            padding: 0.5rem;
            color: var(--primary-blue);
            font-size: 1.25rem;
        }

        /* Right Side - Login Form */
        .auth-form {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }

        .auth-form::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-blue), var(--primary-purple));
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-header h3 {
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: var(--slate-100);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--white);
        }

        .form-input {
            width: 100%;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: var(--white);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            background: rgba(255, 255, 255, 0.1);
        }

        .form-input::placeholder {
            color: var(--slate-100);
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--slate-100);
            font-size: 1.125rem;
        }

        .input-with-icon {
            padding-left: 3rem;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .checkbox-group input[type="checkbox"] {
            width: 1.125rem;
            height: 1.125rem;
            accent-color: var(--primary-blue);
        }

        .checkbox-group label {
            color: var(--slate-100);
            font-size: 0.875rem;
        }

        .forgot-link {
            color: var(--primary-blue);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple));
            color: var(--white);
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .divider {
            text-align: center;
            margin: 1.5rem 0;
            position: relative;
            color: var(--slate-100);
            font-size: 0.875rem;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--glass-border);
        }

        .divider span {
            background: var(--glass-bg);
            padding: 0 1rem;
            position: relative;
            z-index: 1;
        }

        .social-login {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .social-btn {
            padding: 0.75rem;
            border: 1px solid var(--glass-border);
            background: rgba(255, 255, 255, 0.05);
            color: var(--white);
            border-radius: 8px;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .social-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-blue);
        }

        .register-link {
            text-align: center;
            color: var(--slate-100);
            font-size: 0.875rem;
        }

        .register-link a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 500;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        /* Error Messages */
        .error-message {
            background: rgba(244, 63, 94, 0.1);
            border: 1px solid rgba(244, 63, 94, 0.3);
            color: #fca5a5;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }

        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-20px) rotate(120deg); }
            66% { transform: translateY(10px) rotate(240deg); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .auth-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .auth-info {
                text-align: center;
                order: 2;
            }

            .auth-form {
                order: 1;
                padding: 2rem;
            }

            .welcome-text h2 {
                font-size: 2rem;
            }

            .social-login {
                grid-template-columns: 1fr;
            }
        }

        /* Loading State */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .loading .submit-btn {
            position: relative;
        }

        .loading .submit-btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid transparent;
            border-top: 2px solid var(--white);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Floating Background Elements -->
    <div class="floating-element"></div>
    <div class="floating-element"></div>
    <div class="floating-element"></div>

    <div class="auth-container">
        <!-- Left Side - Info -->
        <div class="auth-info" data-aos="fade-right">
            <div class="logo-section">
                <div class="logo-icon">
                    <i class="fas fa-archive"></i>
                </div>
                <div class="logo-text">
                    <h1>ARSIPIN</h1>
                    <p>Sistem Arsip Digital</p>
                </div>
        </div>

            <div class="welcome-text">
                <h2>Selamat Datang di ARSIPIN</h2>
                <p>Masuk ke sistem arsip digital yang dirancang khusus untuk DPMPTSP Provinsi Jawa Timur. Kelola arsip dengan mudah dan aman.</p>
                        </div>

            <ul class="features-list">
                <li>
                    <div class="feature-icon">
                        <i class="fas fa-folder-tree"></i>
                        </div>
                    <span>Kelola arsip induk dan arsip terkait dengan mudah</span>
                </li>
                <li>
                    <div class="feature-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <span>Cari arsip dengan cepat tanpa membedakan huruf besar/kecil</span>
                </li>
                <li>
                    <div class="feature-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <span>Operasi massal untuk efisiensi kerja</span>
                </li>
                <li>
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                </div>
                    <span>Dashboard analitik dan laporan retensi</span>
                </li>
            </ul>
                </div>

        <!-- Right Side - Login Form -->
        <div class="auth-form" data-aos="fade-left">
            <div class="form-header">
                <h3>Masuk ke Sistem</h3>
                <p>Gunakan email dan password Anda untuk mengakses ARSIPIN</p>
            </div>

            @if ($errors->any())
                <div class="error-message">
                    <strong>Terjadi kesalahan:</strong>
                    <ul style="margin-top: 0.5rem; margin-left: 1rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" id="email" name="email" class="form-input input-with-icon"
                               value="{{ old('email') }}" placeholder="Masukkan email Anda" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" class="form-input input-with-icon"
                               placeholder="Masukkan password Anda" required>
                    </div>
                </div>

                <div class="form-options">
                    <div class="checkbox-group">
                        <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember">Ingat saya</label>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
                    @endif
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Masuk
                    </button>
            </form>

            {{-- <div class="divider">
                <span>atau</span>
            </div> --}}

            {{-- <div class="social-login">
                <a href="#" class="social-btn">
                    <i class="fab fa-google"></i>
                    Google
                </a>
                <a href="#" class="social-btn">
                    <i class="fab fa-microsoft"></i>
                    Microsoft
                </a>
            </div> --}}

            <div class="register-link">
                Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
            </div>
        </div>
    </div>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });

        // Form submission handling
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const form = this;
            const submitBtn = form.querySelector('.submit-btn');

            // Add loading state
            form.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        });

        // Input focus effects
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });

            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>
