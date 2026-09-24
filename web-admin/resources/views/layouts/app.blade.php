<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - CIKA Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; }
        .sidebar { width: 250px; min-height: 100vh; background: #1f2937; }
        .sidebar .brand { color: #fff; font-weight: 700; font-size: 1.25rem; padding: 1rem 1.25rem; border-bottom: 1px solid #374151; }
        .sidebar .menu-title { color: #9ca3af; font-size: .7rem; text-transform: uppercase; letter-spacing: .08em; padding: 1rem 1.25rem .25rem; }
        .sidebar a { display: block; color: #d1d5db; text-decoration: none; padding: .45rem 1.25rem; font-size: .9rem; }
        .sidebar a:hover, .sidebar a.active { background: #374151; color: #fff; }
        .sidebar a.sub { padding-left: 2.25rem; }
    </style>
</head>
<body>
<div class="d-flex">
    <nav class="sidebar flex-shrink-0">
        <div class="brand">CIKA Admin</div>

        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>

        <div class="menu-title">Modules</div>
        <a href="{{ route('modules.index') }}" class="sub {{ request()->routeIs('modules.index', 'modules.show', 'modules.edit') ? 'active' : '' }}">All Modules</a>
        <a href="{{ route('modules.create') }}" class="sub {{ request()->routeIs('modules.create') ? 'active' : '' }}">Upload Module</a>
        <a href="{{ route('categories.index') }}" class="sub {{ request()->routeIs('categories.*') ? 'active' : '' }}">Categories</a>
        <a href="#" class="sub">Versions</a>

        <div class="menu-title">Knowledge</div>
        <a href="#" class="sub">Documents</a>
        <a href="#" class="sub">Processing</a>
        <a href="#" class="sub">Chunks</a>

        <div class="menu-title">Monitoring</div>
        <a href="#" class="sub">Chat Logs</a>
        <a href="#" class="sub">Unanswered Questions</a>
        <a href="#" class="sub">Feedback</a>

        <div class="menu-title">System</div>
        <a href="#" class="sub">Users</a>
        <a href="#" class="sub">Settings</a>
        <div class="menu-title">Akun</div>
        <div class="px-3 py-2 text-white-50 small">{{ auth()->user()->name }}</div>
        <form method="POST" action="{{ route('logout') }}" class="px-3 pb-3">
            @csrf
            <button type="submit" class="btn btn-outline-light btn-sm w-100">Logout</button>
        </form>
    </nav>

    <main class="flex-grow-1 p-4">
        <h4 class="mb-4">@yield('heading', 'Dashboard')</h4>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
