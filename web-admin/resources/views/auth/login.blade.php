<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — CIKA Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        /* Background decorative blobs */
        body::before, body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: .25;
            pointer-events: none;
        }
        body::before {
            width: 500px; height: 500px;
            background: radial-gradient(circle, #3b82f6, transparent);
            top: -100px; left: -100px;
        }
        body::after {
            width: 400px; height: 400px;
            background: radial-gradient(circle, #8b5cf6, transparent);
            bottom: -80px; right: -80px;
        }

        .login-card {
            width: 420px;
            background: rgba(255,255,255,.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 24px 60px rgba(0,0,0,.5);
            position: relative; z-index: 1;
        }
        .brand-icon {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; font-weight: 700; color: white;
            margin: 0 auto 16px;
            box-shadow: 0 8px 24px rgba(99,102,241,.4);
        }
        h4 { color: #f8fafc; font-weight: 700; }
        .subtitle { color: #64748b; font-size: .85rem; }

        .form-label { color: #94a3b8; font-size: .82rem; font-weight: 500; }
        .form-control {
            background: rgba(255,255,255,.07);
            border: 1.5px solid rgba(255,255,255,.1);
            border-radius: 10px;
            color: #f8fafc;
            font-size: .9rem;
            padding: 10px 14px;
            transition: border-color .15s, box-shadow .15s;
        }
        .form-control::placeholder { color: #475569; }
        .form-control:focus {
            background: rgba(255,255,255,.1);
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,.2);
            color: #f8fafc;
        }
        .form-control.is-invalid { border-color: #ef4444; }
        .invalid-feedback { color: #f87171; font-size: .78rem; }
        .form-check-label { color: #94a3b8; font-size: .82rem; }
        .form-check-input:checked { background-color: #3b82f6; border-color: #3b82f6; }

        .btn-login {
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            padding: 11px;
            font-size: .92rem;
            transition: all .2s;
            box-shadow: 0 4px 16px rgba(99,102,241,.4);
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(99,102,241,.5);
            color: white;
        }
        .divider { color: #334155; font-size: .75rem; text-align: center; margin: 8px 0; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand-icon">C</div>
        <h4 class="text-center mb-1">Selamat Datang</h4>
        <p class="subtitle text-center mb-4">Masuk ke CIKA Admin Panel</p>

        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Alamat Email</label>
                <div class="input-group">
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="admin@cika.test" required autofocus>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <div class="form-check mb-4">
                <input type="checkbox" name="remember" value="1" class="form-check-input" id="remember">
                <label class="form-check-label" for="remember">Ingat saya</label>
            </div>
            <button type="submit" class="btn btn-login w-100">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
            </button>
        </form>

        <p class="text-center mt-4 mb-0" style="color:#334155;font-size:.72rem;">
            CIKA — Chat Intelligence &amp; Knowledge Assistant
        </p>
    </div>
</body>
</html>
