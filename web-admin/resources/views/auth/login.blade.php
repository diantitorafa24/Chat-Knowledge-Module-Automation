<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — CIKA Module Automation</title>
    <link rel="icon" type="image/png" href="{{ asset('images/cika-icon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #2563eb;
            --primary-indigo: #4f46e5;
            --bg-dark: #090d16;
            --card-radius: 24px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-dark);
            background-image: 
                radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.18) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(79, 70, 229, 0.16) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(15, 23, 42, 0.95) 0px, transparent 100%);
            min-height: 100vh;
            color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            margin: 0;
            padding: 32px 16px;
        }

        /* Ambient background glow particles */
        .ambient-glow-1 {
            position: fixed;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.14) 0%, transparent 70%);
            top: -150px;
            left: -150px;
            pointer-events: none;
            z-index: 0;
        }

        .ambient-glow-2 {
            position: fixed;
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.12) 0%, transparent 70%);
            bottom: -100px;
            right: -100px;
            pointer-events: none;
            z-index: 0;
        }

        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 1140px;
            margin: auto;
        }

        /* ─── SISI KIRI: BRANDING & KATA-KATA PENJELAS ─── */
        .badge-system {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(37, 99, 235, 0.12);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.28);
            padding: 6px 14px;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 24px;
            backdrop-filter: blur(8px);
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background-color: #3b82f6;
            border-radius: 50%;
            box-shadow: 0 0 10px #3b82f6;
            animation: pulseDot 2s infinite ease-in-out;
        }

        @keyframes pulseDot {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.35); opacity: 0.55; }
        }

        .hero-title {
            font-size: 2.85rem;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -0.03em;
            color: #ffffff;
            margin-bottom: 20px;
        }

        .hero-title .gradient-text {
            background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-desc {
            font-size: 1.05rem;
            line-height: 1.65;
            color: #94a3b8;
            max-width: 480px;
            margin-bottom: 32px;
            font-weight: 400;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #cbd5e1;
            font-size: 0.92rem;
            font-weight: 500;
            margin-bottom: 14px;
        }

        .feature-icon {
            width: 30px;
            height: 30px;
            background: rgba(37, 99, 235, 0.15);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #60a5fa;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        /* ─── SISI KANAN: HEADER LOGO SESUAI GAMBAR ASLI ─── */
        .right-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
            text-align: center;
        }

        /* KONTENER LOGO: Card badge dengan efek transparan pekat (dense frosted glassmorphism) */
        .cika-logo-badge {
            display: inline-flex;
            align-items: center;
            gap: 18px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px) saturate(160%);
            -webkit-backdrop-filter: blur(20px) saturate(160%);
            border: 1.5px solid rgba(255, 255, 255, 0.65);
            border-radius: 22px;
            padding: 14px 26px 14px 20px;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.4), 
                        0 0 25px rgba(59, 130, 246, 0.15),
                        inset 0 1px 0 rgba(255, 255, 255, 0.8);
            margin-bottom: 6px;
            transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
            cursor: default;
            user-select: none;
        }

        .cika-logo-badge:hover {
            transform: translateY(-3px) scale(1.02);
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 24px 50px -10px rgba(0, 0, 0, 0.45), 
                        0 0 35px rgba(59, 130, 246, 0.25),
                        inset 0 1px 0 rgba(255, 255, 255, 0.9);
        }

        /* 1. Animasi untuk LOGO (Ikon Gelembung di sebelah kiri) */
        .logo-bubble-anim {
            width: 68px;
            height: 68px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: floatBubble 3.5s ease-in-out infinite;
        }

        .logo-bubble-anim img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(37, 99, 235, 0.2));
        }

        @keyframes floatBubble {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-6px) rotate(-1.5deg);
            }
        }

        /* 2. Animasi untuk KALIMAT SAMPING LOGO (Teks CIKA dan Subtitle di sebelah kanan) */
        .logo-text-anim {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            text-align: left;
            animation: shimmerText 3.5s ease-in-out infinite alternate;
        }

        @keyframes shimmerText {
            0% {
                filter: drop-shadow(0 0 0px rgba(37, 99, 235, 0));
            }
            100% {
                filter: drop-shadow(0 2px 10px rgba(37, 99, 235, 0.35));
            }
        }

        /* Tulisan CIKA: Biru Segar + Efek Transparan Blur di dalam huruf (bukan hitam) */
        .cika-brand-name {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 2.85rem;
            font-weight: 900;
            line-height: 0.95;
            letter-spacing: -0.01em;
            color: #1d4ed8;
            position: relative;
            display: inline-block;
            text-shadow: 0 2px 8px rgba(29, 78, 216, 0.25);
        }

        /* Lapisan transparan blur di dalam/belakang huruf CIKA menggantikan bayangan hitam */
        .cika-brand-name::before {
            content: 'CIKA';
            position: absolute;
            left: 2px;
            top: 2px;
            z-index: -1;
            color: rgba(37, 99, 235, 0.25);
            -webkit-text-fill-color: rgba(37, 99, 235, 0.25);
            filter: blur(4px);
            transform: scale(1.02);
        }

        /* Subtitle: CHAT & KNOWLEDGE MODULE AUTOMATION */
        .cika-brand-sub {
            display: flex;
            flex-direction: column;
            font-size: 0.65rem;
            font-weight: 700;
            color: #334155;
            letter-spacing: 0.16em;
            line-height: 1.35;
            margin-top: 6px;
            text-transform: uppercase;
        }

        /* ─── KARTU LOGIN PUTIH MODERN ─── */
        .auth-card {
            background: #ffffff;
            border-radius: var(--card-radius);
            padding: 38px 36px;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.1);
            color: #1e293b;
            position: relative;
        }

        .auth-card .card-badge {
            color: #2563eb;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            display: block;
            margin-bottom: 6px;
        }

        .auth-card .card-heading {
            font-size: 1.45rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
            margin-bottom: 6px;
        }

        .auth-card .card-subheading {
            color: #64748b;
            font-size: 0.85rem;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        .form-floating-custom {
            position: relative;
            margin-bottom: 18px;
        }

        .form-floating-custom label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        .input-icon-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon-wrap .icon-left {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            font-size: 1.05rem;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .form-control-custom {
            width: 100%;
            height: 48px;
            padding: 10px 14px 10px 42px;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            background: #ffffff;
            font-size: 0.92rem;
            color: #0f172a;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .form-control-custom:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .input-icon-wrap:focus-within .icon-left {
            color: #2563eb;
        }

        .btn-toggle-pwd {
            position: absolute;
            right: 12px;
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 6px;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.15s ease;
        }

        .btn-toggle-pwd:hover {
            color: #334155;
        }

        .form-check-label {
            font-size: 0.85rem;
            color: #475569;
            font-weight: 500;
            user-select: none;
        }

        .form-check-input {
            width: 17px;
            height: 17px;
            border-radius: 5px;
            border: 1.5px solid #cbd5e1;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: #2563eb;
            border-color: #2563eb;
        }

        .btn-login-submit {
            width: 100%;
            height: 48px;
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            border: none;
            border-radius: 12px;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.01em;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.35);
            transition: all 0.25s ease;
            cursor: pointer;
        }

        .btn-login-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.45);
            color: #ffffff;
        }

        .btn-login-submit:active {
            transform: translateY(0);
        }

        .copyright-text {
            color: #64748b;
            font-size: 0.78rem;
            text-align: center;
            margin-top: 22px;
        }

        /* Responsive tweaks */
        @media (max-width: 991px) {
            .hero-section {
                text-align: center;
                margin-bottom: 36px;
            }
            .hero-desc {
                margin-left: auto;
                margin-right: auto;
            }
            .feature-list {
                display: inline-block;
                text-align: left;
            }
            .hero-title {
                font-size: 2.2rem;
            }
        }

        /* HP: form login langsung terlihat, branding diringkas */
        @media (max-width: 767.98px) {
            body { padding: 20px 14px; align-items: flex-start; min-height: 100dvh; }
            .login-wrapper .row { --bs-gutter-x: 1rem; --bs-gutter-y: 1.25rem; }
            body { overflow-x: hidden; }
            .hero-section { order: 2; margin-bottom: 0; }
            .badge-system { font-size: .62rem; padding: 5px 12px; margin-bottom: 14px; }
            .hero-title { font-size: 1.55rem; margin-bottom: 10px; }
            .hero-title br { display: none; }
            .hero-desc { font-size: .88rem; margin-bottom: 0; }
            .feature-list { display: none; }

            .right-header { margin-bottom: 14px; }
            .cika-logo-badge { gap: 12px; padding: 10px 18px 10px 14px; border-radius: 18px; }
            .logo-bubble-anim { width: 48px; height: 48px; }
            .cika-brand-name { font-size: 2rem; }
            .cika-brand-sub { font-size: .55rem; letter-spacing: .12em; }

            .auth-card { padding: 26px 20px; border-radius: 20px; }
            .auth-card .card-heading { font-size: 1.25rem; }
            .form-control-custom { font-size: 16px; } /* cegah auto-zoom iOS */
            .ambient-glow-1, .ambient-glow-2 { width: 320px; height: 320px; }
        }
    </style>
</head>
<body>

    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>

    <div class="login-wrapper">
        <div class="row align-items-center justify-content-between g-5">
            
            {{-- ─── SISI KIRI: BRANDING & KATA-KATA PENJELAS TERPADU ─── --}}
            <div class="col-lg-6 hero-section">
                <div class="badge-system">
                    <span class="pulse-dot"></span>
                    <span>MODULE AUTOMATION SYSTEM</span>
                </div>

                <h1 class="hero-title">
                    Pusat Intelijensi &amp; <br>
                    <span class="gradient-text">Pengetahuan Modul.</span>
                </h1>

                <p class="hero-desc">
                    Dashboard CIKA membantu seluruh tim menemukan SOP instan, mempermudah troubleshooting teknis, dan mengotomasi modul pengetahuan Disacloud secara akurat setiap hari.
                </p>

                <div class="feature-list">
                    <div class="feature-item">
                        <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
                        <span>Akses aman berbasis peran (Superadmin &amp; Admin)</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="bi bi-search"></i></div>
                        <span>Pencarian SOP instan via Qdrant Vector Engine</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="bi bi-robot"></i></div>
                        <span>Kecerdasan AI terpadu untuk seluruh divisi</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="bi bi-chat-dots"></i></div>
                        <span>Integrasi bot Telegram &amp; evaluasi feedback realtime</span>
                    </div>
                </div>
            </div>

            {{-- ─── SISI KANAN: LOGO SESUAI GAMBAR ASLI & FORM LOGIN ─── --}}
            <div class="col-lg-5 col-md-8 mx-auto">
                
                {{-- Header: Logo Badge Lengkap persis seperti gambar pengguna --}}
                <div class="right-header">
                    <div class="cika-logo-badge">
                        {{-- Animasi Logo (Gelembung Tanya di Kiri) --}}
                        <div class="logo-bubble-anim">
                            <img src="{{ asset('images/cika-icon.png') }}" alt="Logo CIKA">
                        </div>

                        {{-- Animasi Kalimat Samping Logo (Teks CIKA + Subtitle di Kanan) --}}
                        <div class="logo-text-anim">
                            <div class="cika-brand-name">CIKA</div>
                            <div class="cika-brand-sub">
                                <span>CHAT &amp; KNOWLEDGE</span>
                                <span>MODULE AUTOMATION</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kartu Login Putih Modern --}}
                <div class="auth-card">
                    <span class="card-badge">Masuk ke akun</span>
                    <h2 class="card-heading">Selamat datang kembali</h2>
                    <p class="card-subheading">Masukkan kredensial Anda untuk melanjutkan ke dashboard CIKA.</p>

                    @if ($errors->any())
                        <div class="alert alert-danger py-2 px-3 mb-3 d-flex align-items-center gap-2" style="font-size:0.85rem; border-radius:10px;">
                            <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                            <div>{{ $errors->first() }}</div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.attempt') }}">
                        @csrf

                        {{-- Input Email --}}
                        <div class="form-floating-custom">
                            <label for="email">Email Pengguna</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-envelope icon-left"></i>
                                <input type="email" name="email" id="email" 
                                       class="form-control-custom @error('email') is-invalid @enderror" 
                                       placeholder="admin@cika.test" 
                                       value="{{ old('email') }}" required autofocus>
                            </div>
                        </div>

                        {{-- Input Password dengan Toggle Show/Hide --}}
                        <div class="form-floating-custom">
                            <label for="password">Password</label>
                            <div class="input-icon-wrap">
                                <i class="bi bi-lock icon-left"></i>
                                <input type="password" name="password" id="password" 
                                       class="form-control-custom" 
                                       placeholder="••••••••" required>
                                <button type="button" class="btn-toggle-pwd" id="togglePasswordBtn" title="Tampilkan Password">
                                    <i class="bi bi-eye" id="toggleEyeIcon"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Remember Me --}}
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                                <label class="form-check-label" for="remember">
                                    Ingat saya
                                </label>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit" class="btn-login-submit">
                            <span>Log In</span>
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </form>
                </div>

                {{-- Copyright Footer --}}
                <div class="copyright-text">
                    &copy; {{ date('Y') }} PT Digital Inovasi Solusi Asia (Disacloud). All rights reserved.
                </div>

            </div>

        </div>
    </div>

    <script>
        // Toggle Show / Hide Password
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('toggleEyeIcon');

        if (toggleBtn && passwordInput && eyeIcon) {
            toggleBtn.addEventListener('click', function () {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                if (isPassword) {
                    passwordInput.setAttribute('type', 'text');
                    eyeIcon.classList.remove('bi-eye');
                    eyeIcon.classList.add('bi-eye-slash');
                    toggleBtn.setAttribute('title', 'Sembunyikan Password');
                } else {
                    passwordInput.setAttribute('type', 'password');
                    eyeIcon.classList.remove('bi-eye-slash');
                    eyeIcon.classList.add('bi-eye');
                    toggleBtn.setAttribute('title', 'Tampilkan Password');
                }
            });
        }
    </script>
</body>
</html>
