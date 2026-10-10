<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — CIKA Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --sidebar-active: #3b82f6;
            --sidebar-text: #94a3b8;
            --sidebar-text-active: #ffffff;
            --sidebar-section: #475569;
            --topbar-height: 60px;
            --sidebar-width: 260px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            margin: 0;
            overflow: hidden;
            color: #1e293b;
        }

        /* ─── WRAPPER ─── */
        .app-wrapper { display: flex; height: 100vh; width: 100vw; }

        /* ─── SIDEBAR ─── */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            overflow-y: auto;
            overflow-x: hidden;
            transition: width 0.25s ease;
            scrollbar-width: thin;
            scrollbar-color: #1e293b #0f172a;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 18px 20px;
            border-bottom: 1px solid #1e293b;
        }
        .sidebar-brand .brand-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 1rem; font-weight: 700; flex-shrink: 0;
        }
        .sidebar-brand .brand-text {
            color: #f8fafc; font-weight: 700; font-size: 1rem; letter-spacing: 0.02em;
        }
        .sidebar-brand .brand-sub {
            color: #64748b; font-size: .65rem; line-height: 1;
        }

        .sidebar-section {
            padding: 16px 20px 4px;
            font-size: .65rem;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--sidebar-section);
            font-weight: 600;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 16px 9px 20px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: .85rem;
            font-weight: 500;
            border-radius: 0;
            transition: all 0.15s ease;
            position: relative;
            margin: 1px 10px;
            border-radius: 8px;
        }
        .sidebar-link i { font-size: 1rem; width: 20px; text-align: center; flex-shrink: 0; }
        .sidebar-link:hover {
            background: var(--sidebar-hover);
            color: #e2e8f0;
        }
        .sidebar-link.active {
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: var(--sidebar-text-active);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35);
        }
        .sidebar-link.active i { color: white; }

        .sidebar-footer {
            margin-top: auto;
            padding: 16px;
            border-top: 1px solid #1e293b;
        }
        .sidebar-user {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 10px; border-radius: 10px;
            background: #1e293b; margin-bottom: 10px;
        }
        .sidebar-user .avatar {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 700; font-size: .85rem; flex-shrink: 0;
        }
        .sidebar-user .user-name { color: #e2e8f0; font-size: .82rem; font-weight: 600; }
        .sidebar-user .user-role { color: #64748b; font-size: .7rem; }

        /* ─── MAIN AREA ─── */
        .main-area { flex: 1; display: flex; flex-direction: column; overflow: hidden; }

        /* ─── TOPBAR ─── */
        .topbar {
            height: var(--topbar-height);
            background: white;
            border-bottom: 1px solid #e2e8f0;
            display: flex; align-items: center;
            padding: 0 24px;
            gap: 12px;
            flex-shrink: 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .topbar-title {
            font-size: 1.05rem; font-weight: 700; color: #0f172a; flex: 1;
        }
        .topbar-badge {
            font-size: .72rem; padding: 3px 10px;
            border-radius: 20px; background: #eff6ff; color: #3b82f6;
            font-weight: 600; border: 1px solid #bfdbfe;
        }

        /* ─── CONTENT ─── */
        .main-content { flex: 1; overflow-y: auto; padding: 24px; }

        /* ─── ALERTS ─── */
        .alert { border: none; border-radius: 12px; font-size: .875rem; }
        .alert-success { background: #f0fdf4; color: #166534; border-left: 4px solid #22c55e; }
        .alert-danger  { background: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444; }

        /* ─── CARDS ─── */
        .card {
            border: none !important;
            border-radius: 14px !important;
            box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.04) !important;
            transition: box-shadow 0.2s ease;
        }
        .card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.1) !important; }
        .card-header {
            background: white !important;
            border-bottom: 1px solid #f1f5f9 !important;
            border-radius: 14px 14px 0 0 !important;
            padding: 16px 20px !important;
            font-weight: 600;
            color: #0f172a;
        }

        /* ─── TABLES ─── */
        .table { font-size: .875rem; }
        .table > :not(caption) > * > * { padding: .75rem 1rem; }
        .table-hover > tbody > tr:hover > * { background: #f8fafc; }
        .table thead { background: #f8fafc; }
        .table th { font-weight: 600; color: #64748b; text-transform: uppercase; font-size: .72rem; letter-spacing: .04em; }

        /* ─── BADGES ─── */
        .badge { border-radius: 6px; font-weight: 500; font-size: .72rem; padding: 4px 8px; }

        /* ─── BUTTONS ─── */
        .btn { border-radius: 8px; font-weight: 500; font-size: .875rem; transition: all 0.15s; }
        .btn-primary { background: linear-gradient(135deg, #3b82f6, #6366f1); border: none; }
        .btn-primary:hover { background: linear-gradient(135deg, #2563eb, #4f46e5); box-shadow: 0 4px 12px rgba(99,102,241,.4); }
        .btn-sm { font-size: .78rem; padding: .35rem .75rem; }

        /* ─── FORM ─── */
        .form-control, .form-select {
            border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            font-size: .875rem;
            transition: border-color .15s, box-shadow .15s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,.15);
        }
        .form-label { font-weight: 500; font-size: .85rem; color: #374151; }

        /* ─── SCROLLBAR ─── */
        .main-content::-webkit-scrollbar { width: 5px; }
        .main-content::-webkit-scrollbar-track { background: transparent; }
        .main-content::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .main-content > * { animation: fadeInUp .2s ease; }
    </style>
    @stack('styles')
</head>
<body>
<div class="app-wrapper">

    {{-- ─── SIDEBAR ─── --}}
    <nav class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">C</div>
            <div>
                <div class="brand-text">CIKA Admin</div>
                <div class="brand-sub">Chat Intelligence & Knowledge</div>
            </div>
        </div>

        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>

        <div class="sidebar-section">Modules</div>
        <a href="{{ route('modules.index') }}" class="sidebar-link {{ request()->routeIs('modules.index','modules.show','modules.edit') ? 'active' : '' }}">
            <i class="bi bi-folder2-open"></i> All Modules
        </a>
        <a href="{{ route('modules.create') }}" class="sidebar-link {{ request()->routeIs('modules.create') ? 'active' : '' }}">
            <i class="bi bi-cloud-upload"></i> Upload Module
        </a>
        <a href="{{ route('categories.index') }}" class="sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
            <i class="bi bi-tags"></i> Categories
        </a>
        <a href="{{ route('versions.index') }}" class="sidebar-link {{ request()->routeIs('versions.*') ? 'active' : '' }}">
            <i class="bi bi-layers"></i> Versions
        </a>

        <div class="sidebar-section">Knowledge</div>
        <a href="{{ route('documents.index') }}" class="sidebar-link {{ request()->routeIs('documents.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text"></i> Documents
        </a>
        <a href="{{ route('processing.index') }}" class="sidebar-link {{ request()->routeIs('processing.*') ? 'active' : '' }}">
            <i class="bi bi-cpu"></i> Processing
        </a>
        <a href="{{ route('chunks.index') }}" class="sidebar-link {{ request()->routeIs('chunks.*') ? 'active' : '' }}">
            <i class="bi bi-puzzle"></i> Chunks
        </a>

        <div class="sidebar-section">Monitoring</div>
        <a href="{{ route('chat-logs.index') }}" class="sidebar-link {{ request()->routeIs('chat-logs.index') ? 'active' : '' }}">
            <i class="bi bi-chat-left-text"></i> Chat Logs
        </a>
        <a href="{{ route('chat-logs.unanswered') }}" class="sidebar-link {{ request()->routeIs('chat-logs.unanswered') ? 'active' : '' }}">
            <i class="bi bi-question-circle"></i> Unanswered
        </a>
        <a href="{{ route('feedback.index') }}" class="sidebar-link {{ request()->routeIs('feedback.index') ? 'active' : '' }}">
            <i class="bi bi-hand-thumbs-up"></i> Feedback
        </a>

        <div class="sidebar-section">System</div>
        <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Users
        </a>
        <a href="{{ route('settings.index') }}" class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <i class="bi bi-sliders"></i> Settings
        </a>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div>
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">{{ ucfirst(auth()->user()->role ?? 'admin') }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm w-100" style="background:#1e293b;color:#94a3b8;border:1px solid #334155;">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>
    </nav>

    {{-- ─── MAIN AREA ─── --}}
    <div class="main-area">
        <div class="topbar">
            <div class="topbar-title">@yield('title', 'Dashboard')</div>
            <span class="topbar-badge"><i class="bi bi-circle-fill text-success" style="font-size:.5rem;"></i> Online</span>
        </div>
        <div class="main-content">
            @if (session('success'))
                <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
                    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
                    <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
