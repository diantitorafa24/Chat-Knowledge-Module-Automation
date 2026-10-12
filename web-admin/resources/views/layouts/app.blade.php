<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — CIKA Admin</title>
    <link rel="icon" type="image/png" href="{{ asset('images/cika-icon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #090e1a;
            --sidebar-hover: #172136;
            --sidebar-active: #2563eb;
            --sidebar-text: #94a3b8;
            --sidebar-text-active: #ffffff;
            --sidebar-section: #475569;
            --topbar-height: 64px;
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 78px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f1f5f9;
            margin: 0;
            overflow: hidden;
            color: #1e293b;
        }

        /* ─── WRAPPER ─── */
        .app-wrapper { 
            display: flex; 
            height: 100vh; 
            width: 100vw; 
            overflow: hidden;
        }

        /* ─── SIDEBAR ─── */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            background-image: 
                radial-gradient(circle at 10% 0%, rgba(37, 99, 235, 0.12) 0%, transparent 40%),
                radial-gradient(circle at 90% 100%, rgba(79, 70, 229, 0.08) 0%, transparent 40%);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            overflow-y: auto;
            overflow-x: hidden;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            scrollbar-width: thin;
            scrollbar-color: #1e293b #090e1a;
            border-right: 1px solid rgba(255, 255, 255, 0.06);
            z-index: 30;
        }

        /* SIDEBAR BRAND SESUAI LOGO ASLI PENGGUNA */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
            min-height: 72px;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .brand-logo-badge {
            width: 42px;
            height: 42px;
            background: #ffffff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5px;
            flex-shrink: 0;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4), 0 0 16px rgba(59, 130, 246, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.85);
            transition: transform 0.25s ease;
        }

        .brand-logo-badge:hover {
            transform: scale(1.08);
        }

        .brand-bubble-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-details {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            white-space: nowrap;
            transition: opacity 0.2s ease, max-width 0.25s ease;
        }

        .brand-title-wrap {
            display: flex;
            align-items: baseline;
            gap: 4px;
        }

        .brand-title-cika {
            font-weight: 900;
            font-size: 1.25rem;
            color: #3b82f6;
            letter-spacing: -0.02em;
            text-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }

        .brand-title-admin {
            font-size: 0.95rem;
            font-weight: 700;
            color: #f8fafc;
        }

        .brand-subtitle {
            font-size: 0.62rem;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            line-height: 1.2;
            margin-top: 1px;
        }

        /* SECTION HEADERS */
        .sidebar-section {
            padding: 16px 20px 6px;
            font-size: .65rem;
            text-transform: uppercase;
            letter-spacing: .12em;
            color: var(--sidebar-section);
            font-weight: 700;
            transition: all 0.25s ease;
            white-space: nowrap;
        }

        /* MENU LINKS DENGAN EFEK MODERN & HOVER HALUS */
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: .86rem;
            font-weight: 500;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            margin: 2px 12px;
            border-radius: 10px;
            white-space: nowrap;
        }

        .sidebar-link i { 
            font-size: 1.15rem; 
            width: 22px; 
            text-align: center; 
            flex-shrink: 0;
            transition: transform 0.2s ease, color 0.2s ease;
        }

        .sidebar-link:hover {
            background: var(--sidebar-hover);
            color: #f1f5f9;
            transform: translateX(2px);
        }

        .sidebar-link:hover i {
            transform: scale(1.1);
            color: #60a5fa;
        }

        .sidebar-link.active {
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            color: var(--sidebar-text-active);
            font-weight: 600;
            box-shadow: 0 4px 16px rgba(37, 99, 235, 0.4);
        }

        .sidebar-link.active i { 
            color: #ffffff; 
        }

        /* FOOTER USER AREA */
        .sidebar-footer {
            margin-top: auto;
            padding: 14px;
            border-top: 1px solid rgba(255, 255, 255, 0.07);
            background: rgba(0, 0, 0, 0.15);
        }

        .sidebar-user {
            display: flex; 
            align-items: center; 
            gap: 10px;
            padding: 8px 10px; 
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.04); 
            border: 1px solid rgba(255, 255, 255, 0.06);
            margin-bottom: 8px;
            overflow: hidden;
            transition: all 0.25s ease;
        }

        .sidebar-user .avatar {
            width: 34px; 
            height: 34px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 50%;
            display: flex; 
            align-items: center; 
            justify-content: center;
            color: white; 
            font-weight: 700; 
            font-size: .85rem; 
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .sidebar-user .user-details {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            white-space: nowrap;
        }

        .sidebar-user .user-name { 
            color: #e2e8f0; 
            font-size: .82rem; 
            font-weight: 600; 
            text-overflow: ellipsis;
            overflow: hidden;
        }

        .sidebar-user .user-role { 
            color: #64748b; 
            font-size: .7rem; 
            font-weight: 500;
        }

        .btn-logout {
            background: rgba(255, 255, 255, 0.04);
            color: #94a3b8;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border-color: rgba(239, 68, 68, 0.3);
        }

        /* ─── SIDEBAR MINIMIZE / COLLAPSED STATE ─── */
        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar.collapsed .brand-details,
        .sidebar.collapsed .link-text,
        .sidebar.collapsed .user-details,
        .sidebar.collapsed .logout-text {
            display: none !important;
        }

        .sidebar.collapsed .sidebar-brand {
            justify-content: center;
            padding: 14px 0;
        }

        .sidebar.collapsed .sidebar-section {
            height: 1px;
            padding: 0;
            margin: 12px 14px;
            background: rgba(255, 255, 255, 0.08);
            text-indent: -9999px;
            overflow: hidden;
        }

        .sidebar.collapsed .sidebar-link {
            justify-content: center;
            padding: 10px 0;
            margin: 3px 10px;
        }

        .sidebar.collapsed .sidebar-link i {
            margin: 0;
            font-size: 1.25rem;
        }

        .sidebar.collapsed .sidebar-user {
            justify-content: center;
            padding: 6px 0;
            background: transparent;
            border: none;
        }

        .sidebar.collapsed .btn-logout {
            padding: 8px 0;
        }

        /* ─── MAIN AREA ─── */
        .main-area { 
            flex: 1; 
            display: flex; 
            flex-direction: column; 
            overflow: hidden; 
            position: relative;
        }

        /* ─── TOPBAR (NAVBAR) TRANSPARAN DENGAN BLUR KACA ─── */
        .topbar {
            height: var(--topbar-height);
            background: rgba(241, 245, 249, 0.75);
            backdrop-filter: blur(18px) saturate(180%);
            -webkit-backdrop-filter: blur(18px) saturate(180%);
            border-bottom: 1px solid rgba(203, 213, 225, 0.7);
            display: flex; 
            align-items: center;
            padding: 0 24px;
            gap: 16px;
            flex-shrink: 0;
            position: sticky;
            top: 0;
            z-index: 20;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03);
            transition: all 0.25s ease;
        }

        /* TOMBOL TOGGLE MINIMIZE SIDEBAR */
        .btn-sidebar-toggle {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1.5px solid rgba(203, 213, 225, 0.8);
            background: rgba(255, 255, 255, 0.85);
            color: #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            flex-shrink: 0;
        }

        .btn-sidebar-toggle:hover {
            background: #ffffff;
            color: #2563eb;
            border-color: #93c5fd;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.15);
        }

        .btn-sidebar-toggle:active {
            transform: translateY(0);
        }

        .topbar-title {
            font-size: 1.12rem; 
            font-weight: 700; 
            color: #0f172a; 
            flex: 1;
            letter-spacing: -0.01em;
        }

        .topbar-badge {
            font-size: .75rem; 
            padding: 5px 12px;
            border-radius: 20px; 
            background: rgba(239, 246, 255, 0.9); 
            color: #2563eb;
            font-weight: 600; 
            border: 1px solid #bfdbfe;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        /* ─── CONTENT AREA ─── */
        .main-content { 
            flex: 1; 
            overflow-y: auto; 
            padding: 24px; 
        }

        /* ─── ALERTS ─── */
        .alert { border: none; border-radius: 12px; font-size: .875rem; }
        .alert-success { background: #f0fdf4; color: #166534; border-left: 4px solid #22c55e; }
        .alert-danger  { background: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444; }

        /* ─── CARDS MODERN ELEVATED ─── */
        .card {
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
            border-radius: 16px !important;
            box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 6px 18px rgba(0,0,0,.03) !important;
            transition: all 0.25s ease;
            background: #ffffff;
        }
        .card:hover { 
            box-shadow: 0 4px 12px rgba(0,0,0,.06), 0 12px 28px rgba(0,0,0,.05) !important; 
        }
        .card-header {
            background: #ffffff !important;
            border-bottom: 1px solid #f1f5f9 !important;
            border-radius: 16px 16px 0 0 !important;
            padding: 16px 20px !important;
            font-weight: 700;
            color: #0f172a;
        }

        /* ─── TABLES ─── */
        .table { font-size: .875rem; }
        .table > :not(caption) > * > * { padding: .75rem 1rem; }
        .table-hover > tbody > tr:hover > * { background: #f8fafc; }
        .table thead { background: #f8fafc; }
        .table th { font-weight: 600; color: #64748b; text-transform: uppercase; font-size: .72rem; letter-spacing: .04em; }

        /* ─── BADGES ─── */
        .badge { border-radius: 6px; font-weight: 600; font-size: .74rem; padding: 4px 9px; }

        /* ─── BUTTONS ─── */
        .btn { border-radius: 10px; font-weight: 600; font-size: .875rem; transition: all 0.2s; }
        .btn-primary { 
            background: linear-gradient(135deg, #2563eb, #4f46e5); 
            border: none; 
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }
        .btn-primary:hover { 
            background: linear-gradient(135deg, #1d4ed8, #4338ca); 
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.4); 
            transform: translateY(-1px);
        }
        .btn-sm { font-size: .8rem; padding: .4rem .85rem; }

        /* ─── FORM ─── */
        .form-control, .form-select {
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
            font-size: .875rem;
            transition: border-color .15s, box-shadow .15s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }
        .form-label { font-weight: 600; font-size: .85rem; color: #334155; }

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

    {{-- ─── SIDEBAR MODERN DENGAN FITUR MINIMIZE ─── --}}
    <nav class="sidebar" id="mainSidebar">
        {{-- BRAND HEADER SESUAI LOGO ASLI CIKA --}}
        <a href="{{ route('dashboard') }}" class="sidebar-brand" title="CIKA Admin">
            <div class="brand-logo-badge">
                <img src="{{ asset('images/cika-icon.png') }}" alt="Logo CIKA" class="brand-bubble-img">
            </div>
            <div class="brand-details">
                <div class="brand-title-wrap">
                    <span class="brand-title-cika">CIKA</span>
                    <span class="brand-title-admin">Admin</span>
                </div>
                <div class="brand-subtitle">
                    Module Automation
                </div>
            </div>
        </a>

        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Dashboard">
            <i class="bi bi-grid-1x2-fill"></i>
            <span class="link-text">Dashboard</span>
        </a>

        <div class="sidebar-section">Modules</div>
        <a href="{{ route('modules.index') }}" class="sidebar-link {{ request()->routeIs('modules.index','modules.show','modules.edit') ? 'active' : '' }}" title="All Modules">
            <i class="bi bi-folder2-open"></i>
            <span class="link-text">All Modules</span>
        </a>
        <a href="{{ route('modules.create') }}" class="sidebar-link {{ request()->routeIs('modules.create') ? 'active' : '' }}" title="Upload Module">
            <i class="bi bi-cloud-upload"></i>
            <span class="link-text">Upload Module</span>
        </a>
        <a href="{{ route('categories.index') }}" class="sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" title="Categories">
            <i class="bi bi-tags"></i>
            <span class="link-text">Categories</span>
        </a>
        <a href="{{ route('versions.index') }}" class="sidebar-link {{ request()->routeIs('versions.*') ? 'active' : '' }}" title="Versions">
            <i class="bi bi-layers"></i>
            <span class="link-text">Versions</span>
        </a>

        <div class="sidebar-section">Knowledge</div>
        <a href="{{ route('documents.index') }}" class="sidebar-link {{ request()->routeIs('documents.*') ? 'active' : '' }}" title="Documents">
            <i class="bi bi-file-earmark-text"></i>
            <span class="link-text">Documents</span>
        </a>
        <a href="{{ route('processing.index') }}" class="sidebar-link {{ request()->routeIs('processing.*') ? 'active' : '' }}" title="Processing">
            <i class="bi bi-cpu"></i>
            <span class="link-text">Processing</span>
        </a>
        <a href="{{ route('chunks.index') }}" class="sidebar-link {{ request()->routeIs('chunks.*') ? 'active' : '' }}" title="Chunks">
            <i class="bi bi-puzzle"></i>
            <span class="link-text">Chunks</span>
        </a>

        <div class="sidebar-section">Monitoring</div>
        <a href="{{ route('chat-logs.index') }}" class="sidebar-link {{ request()->routeIs('chat-logs.index') ? 'active' : '' }}" title="Chat Logs">
            <i class="bi bi-chat-left-text"></i>
            <span class="link-text">Chat Logs</span>
        </a>
        <a href="{{ route('chat-logs.unanswered') }}" class="sidebar-link {{ request()->routeIs('chat-logs.unanswered') ? 'active' : '' }}" title="Unanswered">
            <i class="bi bi-question-circle"></i>
            <span class="link-text">Unanswered</span>
        </a>
        <a href="{{ route('feedback.index') }}" class="sidebar-link {{ request()->routeIs('feedback.index') ? 'active' : '' }}" title="Feedback">
            <i class="bi bi-hand-thumbs-up"></i>
            <span class="link-text">Feedback</span>
        </a>

        @if(auth()->check() && auth()->user()->role === 'superadmin')
        <div class="sidebar-section">System</div>
        <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}" title="Users">
            <i class="bi bi-people"></i>
            <span class="link-text">Users</span>
        </a>
        <a href="{{ route('settings.index') }}" class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" title="Settings">
            <i class="bi bi-sliders"></i>
            <span class="link-text">Settings</span>
        </a>
        @endif

        {{-- USER PROFILE DI BAWAH SIDEBAR --}}
        <div class="sidebar-footer">
            <div class="sidebar-user" title="{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role ?? 'admin') }})">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="user-details">
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">{{ ucfirst(auth()->user()->role ?? 'admin') }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-logout w-100" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="logout-text">Logout</span>
                </button>
            </form>
        </div>
    </nav>

    {{-- ─── MAIN AREA ─── --}}
    <div class="main-area">
        {{-- NAVBAR TRANSPARAN DENGAN BLUR KACA --}}
        <div class="topbar">
            <button type="button" class="btn-sidebar-toggle" id="toggleSidebarBtn" title="Minimize / Expand Sidebar">
                <i class="bi bi-text-indent-left" id="toggleSidebarIcon"></i>
            </button>
            <div class="topbar-title">@yield('title', 'Dashboard')</div>
            <span class="topbar-badge">
                <i class="bi bi-circle-fill text-success" style="font-size:.5rem;"></i> Online
            </span>
        </div>

        {{-- MAIN CONTENT --}}
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
<script>
    // Fitur Minimize / Expand Sidebar dengan Memory LocalStorage
    const sidebar = document.getElementById('mainSidebar');
    const toggleBtn = document.getElementById('toggleSidebarBtn');
    const toggleIcon = document.getElementById('toggleSidebarIcon');

    // Cek preferensi tersimpan
    const isCollapsed = localStorage.getItem('cika_sidebar_collapsed') === 'true';
    if (isCollapsed && sidebar) {
        sidebar.classList.add('collapsed');
        if (toggleIcon) {
            toggleIcon.classList.remove('bi-text-indent-left');
            toggleIcon.classList.add('bi-text-indent-right');
        }
    }

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            const nowCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('cika_sidebar_collapsed', nowCollapsed);

            if (toggleIcon) {
                if (nowCollapsed) {
                    toggleIcon.classList.remove('bi-text-indent-left');
                    toggleIcon.classList.add('bi-text-indent-right');
                } else {
                    toggleIcon.classList.remove('bi-text-indent-right');
                    toggleIcon.classList.add('bi-text-indent-left');
                }
            }
        });
    }
</script>
@stack('scripts')
</body>
</html>
