<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — CIKA Module Automation</title>
    <link rel="icon" type="image/png" href="{{ asset('images/cika-icon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #3b82f6;
            --primary-indigo: #4f46e5;
            --bg-dark: #0b0f19;
            --card-radius: 24px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-dark);
            background-image: 
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.18) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(79, 70, 229, 0.15) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(15, 23, 42, 0.9) 0px, transparent 100%);
            min-height: 100vh;
            color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            margin: 0;
            padding: 24px 16px;
        }

        /* Ambient background glow particles */
        .ambient-glow-1 {
            position: fixed;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.12) 0%, transparent 70%);
            top: -150px;
            left: -150px;
            pointer-events: none;
            z-index: 0;
        }

        .ambient-glow-2 {
            position: fixed;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
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

        /* ─── LEFT BRANDING SECTION ─── */
        .badge-system {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(59, 130, 246, 0.12);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.25);
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
            50% { transform: scale(1.3); opacity: 0.6; }
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
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 14px;
        }

        .feature-icon {
            width: 28px;
            height: 28px;
            background: rgba(59, 130, 246, 0.15);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #60a5fa;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        /* ─── RIGHT SECTION (HEADER & LOGIN CARD) ─── */
        .right-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 24px;
            text-align: center;
        }

        .logo-box {
            width: 76px;
            height: 76px;
            background: #ffffff;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.35), 0 0 25px rgba(59, 130, 246, 0.25);
            margin-bottom: 16px;
            position: relative;
            animation: floatLogo 4s ease-in-out infinite;
            border: 1px solid rgba(255, 255, 255, 0.8);
            transition: transform 0.3s ease;
        }

        .logo-box:hover {
            transform: scale(1.05) translateY(-4px);
        }

        .logo-box img {
            width: 54px;
            height: 54px;
            object-fit: contain;
        }

        @keyframes floatLogo {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-7px); }
        }

        /* Animasi dan Typography CIKA samping/bawah logo */
        .cika-brand-title {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2px;
            margin-bottom: 4px;
            position: relative;
        }

        /* Efek Huruf CIKA: Transparan Blur dengan Outline Biru Modern */
        .cika-letters {
            background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 900;
            letter-spacing: 0.05em;
            position: relative;
            display: inline-block;
            text-shadow: 0 0 20px rgba(59, 130, 246, 0.4);
            animation: glowLetters 3s ease-in-out infinite alternate;
        }

        /* Glassmorphism shadow / transparent blur di dalam huruf CIKA */
        .cika-letters::after {
            content: 'CIKA';
            position: absolute;
            left: 2px;
            top: 2px;
            z-index: -1;
            color: rgba(255, 255, 255, 0.15);
            -webkit-text-fill-color: rgba(255, 255, 255, 0.15);
            filter: blur(4px);
        }

        @keyframes glowLetters {
            0% { filter: drop-shadow(0 0 8px rgba(59, 130, 246, 0.3)); }
            100% { filter: drop-shadow(0 0 16px rgba(99, 102, 241, 0.6)); }
        }

        .cika-brand-sub {
            color: #94a3b8;
            font-size: 0.82rem;
            font-weight: 500;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            animation: fadeInSub 1.2s ease forwards;
        }

        @keyframes fadeInSub {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ─── LOGIN CARD ─── */
        .auth-card {
            background: #ffffff;
            border-radius: var(--card-radius);
            padding: 40px 38px;
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
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
        }

        .input-icon-wrap:focus-within .icon-left {
            color: #3b82f6;
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
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        .btn-login-submit {
            width: 100%;
            height: 48px;
            background: linear-gradient(135deg, #3b82f6 0%, #4f46e5 100%);
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
            margin-top: 24px;
        }

        /* Responsive tweaks */
        @media (max-width: 991px) {
            .hero-section {
                text-align: center;
                margin-bottom: 40px;
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
    </style>
</head>
<body>

    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>

    <div class="login-wrapper">
        <div class="row align-items-center justify-content-between g-5">
            
            {{-- ─── SISI KIRI: BRANDING & HEADLINE CERDAS (GAYA NISA) ─── --}}
            <div class="col-lg-6 hero-section">
                <div class="badge-system">
                    <span class="pulse-dot"></span>
                    <span>MODULE AUTOMATION SYSTEM</span>
                </div>

                <h1 class="hero-title">
                    Otomasi modul dan <br>
                    <span class="gradient-text">pengetahuan cerdas.</span>
                </h1>

                <p class="hero-desc">
                    Dashboard CIKA membantu seluruh tim menemukan SOP instan, memahami panduan teknis, dan mengotomasi modul pengetahuan Disacloud secara akurat setiap hari.
                </p>

                <div class="feature-list">
                    <div class="feature-item">
                        <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
                        <span>Akses aman berbasis peran (Superadmin &amp; Admin)</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="bi bi-search"></i></div>
                        <span>Pencarian dokumen &amp; gambar instan via Qdrant Vector</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="bi bi-robot"></i></div>
                        <span>Asisten RAG cerdas terintegrasi Google Gemini AI</span>
                    </div>
                </div>
            </div>

            {{-- ─── SISI KANAN: LOGO BERANIMASI & KARTU LOGIN ─── --}}
            <div class="col-lg-5 col-md-8 mx-auto">
                
                {{-- Header Logo & Title dengan Animasi --}}
                <div class="right-header">
                    <div class="logo-box">
                        <img src="{{ asset('images/cika-icon.png') }}" alt="Logo CIKA">
                    </div>
                    
                    <div class="cika-brand-title">
                        <span class="cika-letters">CIKA</span>
                        <span class="ms-2 fw-bold text-white">Assistant</span>
                    </div>
                    <div class="cika-brand-sub">
                        Chat Intelligence &amp; Knowledge Assistant
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
                                       placeholder="nama@disacloud.net" 
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
