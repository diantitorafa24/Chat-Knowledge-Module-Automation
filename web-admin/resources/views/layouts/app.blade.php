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
        <a href="#" class="sub">All Modules</a>
        <a href="#" class="sub">Upload Module</a>
        <a href="#" class="sub">Categories</a>
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
    </nav>

    <main class="flex-grow-1 p-4">
        <h4 class="mb-4">@yield('heading', 'Dashboard')</h4>
        @yield('content')
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
