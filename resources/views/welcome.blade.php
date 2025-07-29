<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARSIPIN - Sistem Arsip Pintar DPMPTSP Provinsi Jawa Timur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.10.4/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.10.4/ScrollTrigger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.10.4/MotionPathPlugin.min.js"></script>
    <style>
        :root {
            --primary-blue: #1e40af;
            --primary-purple: #7c3aed;
            --primary-green: #059669;
            --accent-yellow: #fbbf24;
            --accent-pink: #ec4899;
            --dark-bg: #0f172a;
            --light-bg: #f8fafc;
            --glass-bg: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--dark-bg);
            color: #fff;
            overflow-x: hidden;
            perspective: 1000px;
            background: radial-gradient(ellipse at bottom, #0d1d31 0%, #0c0d13 100%);
            min-height: 100vh;
        }

        /* Floating Particles */
        .floating-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.8) 0%, rgba(255,255,255,0) 70%);
            animation: float 15s infinite linear;
            opacity: 0;
            filter: blur(1px);
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 0 5%;
            position: relative;
            overflow: hidden;
            z-index: 10;
        }

        .hero-content {
            max-width: 1600px;
            margin: 0 auto;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5rem;
            align-items: center;
        }

        .hero-text {
            position: relative;
            z-index: 20;
        }

        .hero-tagline {
            display: inline-block;
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            border-radius: 50px;
            padding: 0.6rem 1.8rem;
            font-size: 1rem;
            margin-bottom: 2rem;
            border: 1px solid var(--glass-border);
            animation: fadeInDown 1s ease-out;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.2);
        }

        .hero-title {
            font-size: 5rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 2rem;
            background: linear-gradient(135deg, #fff 0%, var(--accent-yellow) 50%, var(--accent-pink) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-fill-color: transparent;
            background-size: 300% 300%;
            animation: gradientAnimation 8s ease infinite, textGlow 3s ease-in-out infinite alternate;
            position: relative;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
        }

        .hero-title span {
            display: block;
            font-size: 3.5rem;
            margin-top: 1rem;
        }

        .hero-title::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 0;
            width: 200px;
            height: 6px;
            background: linear-gradient(90deg, var(--primary-blue), var(--primary-green));
            border-radius: 6px;
            animation: lineGrow 1.8s ease-out;
        }

        .hero-description {
            font-size: 1.4rem;
            line-height: 1.7;
            margin-bottom: 3rem;
            max-width: 650px;
            opacity: 0.9;
            animation: fadeInUp 1s 0.3s both;
            text-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        .cta-buttons {
            display: flex;
            gap: 2rem;
            margin-bottom: 4rem;
            animation: fadeInUp 1s 0.5s both;
        }

        .cta-btn {
            padding: 1.4rem 3rem;
            border-radius: 20px;
            font-weight: 700;
            font-size: 1.2rem;
            display: inline-flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            z-index: 1;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border: none;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .cta-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-purple), var(--primary-green));
            z-index: -1;
            opacity: 1;
            transition: opacity 0.4s ease;
        }

        .cta-btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
            z-index: 1;
        }

        .cta-btn:hover::after {
            left: 100%;
        }

        .cta-primary {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-purple) 100%);
            color: white;
            box-shadow: 0 15px 35px rgba(124, 58, 237, 0.4);
        }

        .cta-primary:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 20px 40px rgba(124, 58, 237, 0.6), 0 0 30px rgba(124, 58, 237, 0.4);
        }

        .cta-secondary {
            background: transparent;
            color: white;
            border: 2px solid var(--glass-border);
            backdrop-filter: blur(10px);
        }

        .cta-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: white;
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(255, 255, 255, 0.1);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            max-width: 700px;
            animation: fadeInUp 1s 0.7s both;
        }

        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            padding: 2.2rem 1.5rem;
            text-align: center;
            border: 1px solid var(--glass-border);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            z-index: 1;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-purple) 100%);
            z-index: -1;
            opacity: 0.2;
            transition: opacity 0.4s ease;
        }

        .stat-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .stat-value {
            font-size: 3rem;
            font-weight: 800;
            color: var(--accent-yellow);
            margin-bottom: 0.5rem;
            text-shadow: 0 0 15px rgba(251, 191, 36, 0.5);
        }

        .stat-label {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        /* 3D Holographic Dashboard */
        .holographic-dashboard {
            position: relative;
            width: 100%;
            height: 600px;
            perspective: 2000px;
        }

        .hologram-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 600px;
            height: 450px;
            transform-style: preserve-3d;
            animation: rotate3d 30s infinite linear;
        }

        .hologram-face {
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            box-shadow: 0 0 50px rgba(59, 130, 246, 0.3),
                        inset 0 0 20px rgba(255, 255, 255, 0.1);
            overflow: hidden;
            z-index: 2;
        }

        .hologram-face::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(transparent, rgba(168, 85, 247, 0.4), transparent 30%);
            animation: rotate 4s linear infinite;
            z-index: -1;
        }

        .hologram-title {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 2.5rem;
            background: linear-gradient(90deg, #fff, var(--accent-yellow));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-fill-color: transparent;
            text-align: center;
        }

        .hologram-stats {
            display: flex;
            gap: 3rem;
            margin-bottom: 3rem;
        }

        .hologram-stat {
            text-align: center;
        }

        .hologram-stat-value {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--accent-yellow), var(--accent-pink));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-fill-color: transparent;
        }

        .hologram-stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .hologram-progress {
            width: 100%;
            height: 10px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            overflow: hidden;
            margin-top: 1.5rem;
        }

        .hologram-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-blue), var(--primary-green));
            border-radius: 5px;
            width: 75%;
            animation: progressGrow 3s ease-in-out infinite alternate;
        }

        /* Features Section */
        .features {
            padding: 10rem 5%;
            position: relative;
            overflow: hidden;
        }

        .section-header {
            text-align: center;
            max-width: 900px;
            margin: 0 auto 8rem;
            position: relative;
            z-index: 10;
        }

        .section-title {
            font-size: 4.5rem;
            font-weight: 800;
            margin-bottom: 2rem;
            background: linear-gradient(135deg, #fff 0%, var(--accent-yellow) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-fill-color: transparent;
            text-shadow: 0 0 20px rgba(251, 191, 36, 0.3);
        }

        .section-subtitle {
            font-size: 1.6rem;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.7;
            text-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 4rem;
            max-width: 1600px;
            margin: 0 auto;
            position: relative;
            z-index: 10;
        }

        .feature-card {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border-radius: 30px;
            padding: 4rem 3rem;
            border: 1px solid var(--glass-border);
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            transform: translateY(50px);
            opacity: 0;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-purple) 100%);
            z-index: -1;
            opacity: 0.15;
            transition: opacity 0.4s ease;
        }

        .feature-card:hover {
            transform: translateY(-20px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3), 0 0 40px rgba(124, 58, 237, 0.4);
            background: rgba(30, 64, 175, 0.2);
        }

        .feature-icon {
            width: 100px;
            height: 100px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 3rem;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-purple) 100%);
            box-shadow: 0 15px 40px rgba(124, 58, 237, 0.4);
            font-size: 3rem;
            color: white;
            transition: all 0.4s ease;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.15) rotate(10deg);
            box-shadow: 0 20px 50px rgba(124, 58, 237, 0.6);
        }

        .feature-title {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 2rem;
            color: white;
            text-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }

        .feature-description {
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 3rem;
            line-height: 1.8;
            font-size: 1.3rem;
        }

        .feature-link {
            display: inline-flex;
            align-items: center;
            gap: 1rem;
            font-weight: 700;
            text-decoration: none;
            color: var(--accent-yellow);
            font-size: 1.3rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .feature-link:hover {
            gap: 1.5rem;
            text-shadow: 0 0 15px rgba(251, 191, 36, 0.7);
        }

        /* Animated Footer */
        footer {
            background: rgba(15, 23, 42, 0.95);
            color: white;
            padding: 8rem 5% 4rem;
            position: relative;
            overflow: hidden;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-content {
            max-width: 1600px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 5rem;
            position: relative;
            z-index: 10;
        }

        .footer-brand {
            display: flex;
            flex-direction: column;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .footer-logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-purple) 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            box-shadow: 0 15px 40px rgba(124, 58, 237, 0.4);
            animation: pulse 2s infinite;
        }

        .footer-logo-text h3 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--accent-yellow), var(--accent-pink));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-fill-color: transparent;
        }

        .footer-logo-text p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.2rem;
        }

        .footer-description {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.8;
            margin-bottom: 2rem;
            font-size: 1.3rem;
        }

        .footer-heading {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 2rem;
            position: relative;
            padding-bottom: 1rem;
            background: linear-gradient(90deg, #fff, var(--accent-yellow));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-fill-color: transparent;
        }

        .footer-heading::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 5px;
            background: linear-gradient(to right, var(--primary-blue), var(--primary-purple));
            border-radius: 5px;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 1.5rem;
            transform: translateX(-20px);
            opacity: 0;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 1.3rem;
        }

        .footer-links a:hover {
            color: var(--accent-yellow);
            gap: 1.5rem;
            text-shadow: 0 0 10px rgba(251, 191, 36, 0.5);
        }

        .footer-links a i {
            width: 30px;
            text-align: center;
            color: var(--primary-purple);
            font-size: 1.5rem;
        }

        .copyright {
            text-align: center;
            padding-top: 6rem;
            margin-top: 6rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.6);
            font-size: 1.2rem;
            position: relative;
            z-index: 10;
        }

        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0) translateX(0) rotate(0deg); }
            25% { transform: translateY(-30px) translateX(15px) rotate(5deg); }
            50% { transform: translateY(15px) translateX(-15px) rotate(-5deg); }
            75% { transform: translateY(-25px) translateX(-20px) rotate(3deg); }
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

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes textGlow {
            from { text-shadow: 0 0 10px rgba(255, 255, 255, 0.1); }
            to { text-shadow: 0 0 30px rgba(251, 191, 36, 0.8), 0 0 50px rgba(236, 72, 153, 0.6); }
        }

        @keyframes lineGrow {
            from { width: 0; opacity: 0; }
            to { width: 200px; opacity: 1; }
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes rotate3d {
            0% { transform: translate(-50%, -50%) rotateY(0deg) rotateX(15deg); }
            25% { transform: translate(-50%, -50%) rotateY(90deg) rotateX(15deg); }
            50% { transform: translate(-50%, -50%) rotateY(180deg) rotateX(15deg); }
            75% { transform: translate(-50%, -50%) rotateY(270deg) rotateX(15deg); }
            100% { transform: translate(-50%, -50%) rotateY(360deg) rotateX(15deg); }
        }

        @keyframes progressGrow {
            from { width: 70%; }
            to { width: 85%; }
        }

        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(124, 58, 237, 0.7); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 20px rgba(124, 58, 237, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(124, 58, 237, 0); }
        }

        /* Responsive */
        @media (max-width: 1400px) {
            .hero-title {
                font-size: 4.2rem;
            }

            .holographic-dashboard {
                height: 550px;
            }
        }

        @media (max-width: 1200px) {
            .hero-title {
                font-size: 3.8rem;
            }

            .section-title {
                font-size: 3.8rem;
            }

            .holographic-dashboard {
                height: 500px;
            }
        }

        @media (max-width: 992px) {
            .hero-content {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 4rem;
            }

            .hero-text {
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .cta-buttons {
                justify-content: center;
            }

            .holographic-dashboard {
                height: 450px;
                margin-top: 4rem;
            }

            .section-title {
                font-size: 3.2rem;
            }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 3.2rem;
            }

            .hero-title span {
                font-size: 2.8rem;
            }

            .cta-buttons {
                flex-direction: column;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                max-width: 500px;
            }

            .section-title {
                font-size: 2.8rem;
            }

            .section-subtitle {
                font-size: 1.3rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .holographic-dashboard {
                height: 400px;
            }
        }

        @media (max-width: 576px) {
            .hero-title {
                font-size: 2.8rem;
            }

            .hero-title span {
                font-size: 2.2rem;
            }

            .hero-description {
                font-size: 1.1rem;
            }

            .section-title {
                font-size: 2.3rem;
            }

            .holographic-dashboard {
                height: 350px;
            }
        }
    </style>
</head>
<body>
    <!-- Floating Particles -->
    <div class="floating-particles" id="particles"></div>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <div class="hero-tagline">DPMPTSP Provinsi Jawa Timur</div>
                <h1 class="hero-title">
                    REVOLUSI DIGITAL<br>
                    <span>ARSIPIN</span>
                </h1>
                <p class="hero-description">
                    Sistem Manajemen Arsip Masa Depan yang Mengubah Cara DPMPTSP Provinsi Jawa Timur Mengelola Dokumen. Dengan teknologi AI dan blockchain, kami menghadirkan solusi arsip paling canggih di Indonesia.
                </p>

                <div class="cta-buttons">
                    <a href="#" class="cta-btn cta-primary">
                        <i class="fas fa-rocket"></i> Mulai Revolusi
                    </a>
                    <a href="#features" class="cta-btn cta-secondary">
                        <i class="fas fa-vr-cardboard"></i> Lihat Demo
                    </a>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value">AI-Powered</div>
                        <div class="stat-label">Kecerdasan Buatan</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">Blockchain</div>
                        <div class="stat-label">Keamanan Tingkat Tinggi</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">100%</div>
                        <div class="stat-label">Kesesuaian JRA</div>
                    </div>
                </div>
            </div>

            <div class="holographic-dashboard">
                <div class="hologram-container">
                    <div class="hologram-face" style="transform: translateZ(250px);">
                        <h3 class="hologram-title">DASHBOARD ARSIPIN 4.0</h3>
                        <div class="hologram-stats">
                            <div class="hologram-stat">
                                <div class="hologram-stat-value">156</div>
                                <div class="hologram-stat-label">Arsip Aktif</div>
                            </div>
                            <div class="hologram-stat">
                                <div class="hologram-stat-value">89</div>
                                <div class="hologram-stat-label">Arsip Inaktif</div>
                            </div>
                            <div class="hologram-stat">
                                <div class="hologram-stat-value">98%</div>
                                <div class="hologram-stat-label">Akurasi</div>
                            </div>
                        </div>
                        <div class="hologram-progress">
                            <div class="hologram-progress-bar"></div>
                        </div>
                    </div>
                    <div class="hologram-face" style="transform: rotateY(90deg) translateZ(250px);">
                        <h3 class="hologram-title">ANALISIS KECERDASAN BUATAN</h3>
                        <div class="hologram-stats">
                            <div class="hologram-stat">
                                <div class="hologram-stat-value">0.2s</div>
                                <div class="hologram-stat-label">Waktu Proses</div>
                            </div>
                        </div>
                    </div>
                    <div class="hologram-face" style="transform: rotateY(180deg) translateZ(250px);">
                        <h3 class="hologram-title">KEAMANAN BLOCKCHAIN</h3>
                        <div class="hologram-stats">
                            <div class="hologram-stat">
                                <div class="hologram-stat-value">256-bit</div>
                                <div class="hologram-stat-label">Enkripsi</div>
                            </div>
                        </div>
                    </div>
                    <div class="hologram-face" style="transform: rotateY(270deg) translateZ(250px);">
                        <h3 class="hologram-title">INTEGRASI CLOUD</h3>
                        <div class="hologram-stats">
                            <div class="hologram-stat">
                                <div class="hologram-stat-value">99.9%</div>
                                <div class="hologram-stat-label">Uptime</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="section-header">
            <h2 class="section-title">FITUR <span style="color: var(--accent-yellow);">REVOLUSIONER</span></h2>
            <p class="section-subtitle">Teknologi mutakhir yang mengubah cara Anda mengelola arsip digital</p>
        </div>

        <div class="features-grid">
            <!-- Feature 1 -->
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-brain"></i>
                </div>
                <h3 class="feature-title">Kecerdasan Artifisial</h3>
                <p class="feature-description">
                    Sistem AI canggih yang secara otomatis mengklasifikasikan, menandai, dan mengelola arsip berdasarkan konten dan metadata.
                </p>
                <a href="#" class="feature-link">
                    <span>Pelajari Lebih Lanjut</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <!-- Feature 2 -->
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-link"></i>
                </div>
                <h3 class="feature-title">Teknologi Blockchain</h3>
                <p class="feature-description">
                    Keamanan tingkat militer dengan teknologi blockchain yang menjamin keaslian dan integritas setiap dokumen.
                </p>
                <a href="#" class="feature-link">
                    <span>Pelajari Lebih Lanjut</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <!-- Feature 3 -->
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-cloud"></i>
                </div>
                <h3 class="feature-title">Hybrid Cloud</h3>
                <p class="feature-description">
                    Penyimpanan hybrid yang menggabungkan keamanan penyimpanan lokal dengan skalabilitas cloud publik.
                </p>
                <a href="#" class="feature-link">
                    <span>Pelajari Lebih Lanjut</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <!-- Feature 4 -->
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-robot"></i>
                </div>
                <h3 class="feature-title">Otomasi JRA Pro</h3>
                <p class="feature-description">
                    Sistem otomasi cerdas yang mengelola seluruh siklus hidup arsip sesuai Jadwal Retensi Arsip secara presisi.
                </p>
                <a href="#" class="feature-link">
                    <span>Pelajari Lebih Lanjut</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <!-- Feature 5 -->
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3 class="feature-title">Keamanan Multi-Layer</h3>
                <p class="feature-description">
                    Perlindungan 7 lapis dengan enkripsi end-to-end, autentikasi biometrik, dan sistem deteksi intrusi canggih.
                </p>
                <a href="#" class="feature-link">
                    <span>Pelajari Lebih Lanjut</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <!-- Feature 6 -->
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="feature-title">Analitik Prediktif</h3>
                <p class="feature-description">
                    Sistem analitik cerdas yang memprediksi tren arsip dan memberikan rekomendasi manajemen proaktif.
                </p>
                <a href="#" class="feature-link">
                    <span>Pelajari Lebih Lanjut</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-brand">
                <div class="footer-logo">
                    <div class="footer-logo-icon">
                        <i class="fas fa-archive"></i>
                    </div>
                    <div class="footer-logo-text">
                        <h3>ARSIPIN</h3>
                        <p>Next Generation Archiving</p>
                    </div>
                </div>
                <p class="footer-description">
                    Sistem Manajemen Arsip Digital Terintegrasi DPMPTSP Provinsi Jawa Timur yang menghadirkan revolusi dalam pengelolaan dokumen pemerintah dengan teknologi terkini.
                </p>
            </div>

            <div class="footer-links-container">
                <h4 class="footer-heading">Navigasi</h4>
                <ul class="footer-links">
                    <li><a href="#"><i class="fas fa-home"></i> Beranda</a></li>
                    <li><a href="#features"><i class="fas fa-star"></i> Fitur</a></li>
                    <li><a href="#"><i class="fas fa-info-circle"></i> Tentang</a></li>
                    <li><a href="#"><i class="fas fa-newspaper"></i> Berita</a></li>
                    <li><a href="#"><i class="fas fa-headset"></i> Kontak</a></li>
                </ul>
            </div>

            <div class="footer-links-container">
                <h4 class="footer-heading">Teknologi</h4>
                <ul class="footer-links">
                    <li><a href="#"><i class="fas fa-microchip"></i> AI & Machine Learning</a></li>
                    <li><a href="#"><i class="fas fa-link"></i> Blockchain</a></li>
                    <li><a href="#"><i class="fas fa-cloud"></i> Hybrid Cloud</a></li>
                    <li><a href="#"><i class="fas fa-shield-alt"></i> Keamanan</a></li>
                    <li><a href="#"><i class="fas fa-database"></i> Big Data</a></li>
                </ul>
            </div>

            <div class="footer-links-container">
                <h4 class="footer-heading">Dukungan</h4>
                <ul class="footer-links">
                    <li><a href="#"><i class="fas fa-book-open"></i> Dokumentasi</a></li>
                    <li><a href="#"><i class="fas fa-video"></i> Tutorial VR</a></li>
                    <li><a href="#"><i class="fas fa-download"></i> Download</a></li>
                    <li><a href="#"><i class="fas fa-lock"></i> Kebijakan Privasi</a></li>
                    <li><a href="#"><i class="fas fa-file-contract"></i> Persyaratan</a></li>
                </ul>
            </div>
        </div>

        <div class="copyright">
            &copy; 2023 ARSIPIN - DPMPTSP Provinsi Jawa Timur. Semua hak dilindungi. Rekayasa Masa Depan.
        </div>
    </footer>

    <script>
        // Create floating particles
        const createParticles = () => {
            const container = document.getElementById('particles');
            const colors = ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ec4899'];
            const particleCount = 150;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');

                const size = Math.random() * 20 + 5;
                const color = colors[Math.floor(Math.random() * colors.length)];
                const duration = Math.random() * 20 + 10;
                const delay = Math.random() * -20;

                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                particle.style.background = `radial-gradient(circle, ${color} 0%, rgba(255,255,255,0) 70%)`;
                particle.style.left = `${Math.random() * 100}%`;
                particle.style.top = `${Math.random() * 100}%`;
                particle.style.animationDuration = `${duration}s`;
                particle.style.animationDelay = `${delay}s`;
                particle.style.opacity = Math.random() * 0.6 + 0.1;

                container.appendChild(particle);
            }
        };

        // GSAP Animations
        const initAnimations = () => {
            // Register plugins
            gsap.registerPlugin(ScrollTrigger, MotionPathPlugin);

            // Animate feature cards on scroll
            gsap.utils.toArray('.feature-card').forEach((card, i) => {
                gsap.fromTo(card,
                    { y: 100, opacity: 0 },
                    {
                        y: 0,
                        opacity: 1,
                        duration: 1.2,
                        delay: i * 0.15,
                        scrollTrigger: {
                            trigger: card,
                            start: "top 90%",
                            toggleActions: "play none none none"
                        },
                        ease: "elastic.out(1, 0.8)"
                    }
                );
            });

            // Animate footer links
            gsap.utils.toArray('.footer-links li').forEach((item, i) => {
                gsap.fromTo(item,
                    { x: -30, opacity: 0 },
                    {
                        x: 0,
                        opacity: 1,
                        duration: 0.8,
                        delay: i * 0.15,
                        scrollTrigger: {
                            trigger: "footer",
                            start: "top 90%",
                            toggleActions: "play none none none"
                        },
                        ease: "back.out(2)"
                    }
                );
            });

            // Create floating path for hologram container
            gsap.to(".hologram-container", {
                motionPath: {
                    path: [
                        {x: 0, y: 0},
                        {x: 10, y: -15},
                        {x: 0, y: 0},
                        {x: -10, y: 15},
                        {x: 0, y: 0}
                    ],
                    curviness: 1.5
                },
                duration: 15,
                repeat: -1,
                ease: "none"
            });

            // Hero title animation
            gsap.from('.hero-title', {
                y: -50,
                opacity: 0,
                duration: 1.5,
                ease: "power4.out"
            });

            // Create continuous rotation for hologram
            gsap.to(".hologram-container", {
                rotationY: 360,
                duration: 40,
                repeat: -1,
                ease: "none"
            });
        };

        // Initialize everything when page loads
        window.addEventListener('load', () => {
            createParticles();
            initAnimations();

            // Start hologram animations
            document.querySelectorAll('.hologram-progress-bar').forEach(bar => {
                gsap.to(bar, {
                    width: "85%",
                    duration: 3,
                    repeat: -1,
                    yoyo: true,
                    ease: "sine.inOut"
                });
            });
        });
    </script>
</body>
</html>
