<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0c1210;
            --sidebar: #121a17;
            --panel: #18221d;
            --ink: #e8f0ec;
            --muted: #8aa396;
            --line: #2a4037;
            --accent: #3ecf8e;
            --accent-ink: #062216;
            --warn: #e8b86d;
            --danger: #f07178;
            --radius: 12px;
            --font: "DM Sans", system-ui, sans-serif;
            --display: "Fraunces", Georgia, serif;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: var(--font);
            color: var(--ink);
            background: var(--bg);
        }
        .shell {
            display: grid;
            grid-template-columns: 240px 1fr;
            min-height: 100vh;
        }
        .sidebar {
            background: linear-gradient(180deg, #15201b 0%, var(--sidebar) 100%);
            border-right: 1px solid var(--line);
            padding: 1.5rem 1rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .brand {
            font-family: var(--display);
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            padding: 0 0.5rem;
            margin: 0;
        }
        .brand span { color: var(--accent); }
        .nav { display: flex; flex-direction: column; gap: 0.25rem; }
        .nav a {
            display: block;
            padding: 0.7rem 0.85rem;
            border-radius: 10px;
            color: var(--muted);
            text-decoration: none;
            font-weight: 500;
            transition: background 0.15s ease, color 0.15s ease;
        }
        .nav a:hover { color: var(--ink); background: color-mix(in srgb, white 5%, transparent); }
        .nav a.active {
            color: var(--accent-ink);
            background: var(--accent);
        }
        .main {
            padding: 2rem 2.25rem 3rem;
            background:
                radial-gradient(900px 400px at 100% 0%, #1d332a 0%, transparent 55%),
                var(--bg);
        }
        .page-head {
            margin-bottom: 1.5rem;
        }
        .page-head h1 {
            font-family: var(--display);
            font-size: clamp(1.6rem, 3vw, 2rem);
            margin: 0 0 0.35rem;
            letter-spacing: -0.02em;
        }
        .page-head p { margin: 0; color: var(--muted); }
        .flash {
            margin-bottom: 1rem;
            padding: 0.75rem 1rem;
            border-radius: var(--radius);
            background: color-mix(in srgb, var(--accent) 18%, transparent);
            color: #b8f5d4;
            font-size: 0.92rem;
        }
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 0.85rem;
            margin-bottom: 1.5rem;
        }
        .card {
            background: color-mix(in srgb, var(--panel) 92%, black);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 1rem 1.1rem;
        }
        .card .label { color: var(--muted); font-size: 0.82rem; margin-bottom: 0.35rem; }
        .card .value { font-size: 1.6rem; font-weight: 700; letter-spacing: -0.03em; }
        .panel {
            background: color-mix(in srgb, var(--panel) 92%, black);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            overflow: hidden;
        }
        .panel-head {
            padding: 0.9rem 1.1rem;
            border-bottom: 1px solid var(--line);
            font-weight: 600;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            text-align: left;
            padding: 0.8rem 1.1rem;
            border-bottom: 1px solid color-mix(in srgb, var(--line) 70%, transparent);
            font-size: 0.92rem;
        }
        th { color: var(--muted); font-weight: 500; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.04em; }
        tr:last-child td { border-bottom: 0; }
        a.row-link { color: var(--ink); text-decoration: none; font-weight: 600; }
        a.row-link:hover { color: var(--accent); }
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.15rem 0.5rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge.ok { background: color-mix(in srgb, var(--accent) 20%, transparent); color: var(--accent); }
        .badge.off { background: color-mix(in srgb, var(--muted) 20%, transparent); color: var(--muted); }
        .badge.done { background: color-mix(in srgb, var(--accent) 20%, transparent); color: var(--accent); }
        .badge.open { background: color-mix(in srgb, var(--warn) 20%, transparent); color: var(--warn); }
        .muted { color: var(--muted); }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 0.75rem;
            padding: 1.1rem;
            border-bottom: 1px solid var(--line);
        }
        label { display: flex; flex-direction: column; gap: 0.35rem; font-size: 0.8rem; color: var(--muted); }
        input, select {
            font: inherit;
            color: var(--ink);
            background: color-mix(in srgb, var(--bg) 80%, black);
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 0.65rem 0.75rem;
        }
        input:focus, select:focus {
            outline: none;
            border-color: color-mix(in srgb, var(--accent) 60%, var(--line));
        }
        .btn {
            font: inherit;
            border: 0;
            cursor: pointer;
            border-radius: 8px;
            padding: 0.65rem 1rem;
            font-weight: 600;
        }
        .btn-primary { background: var(--accent); color: var(--accent-ink); }
        .btn-ghost {
            background: transparent;
            color: var(--muted);
            padding: 0.35rem 0.5rem;
        }
        .btn-ghost:hover { color: var(--ink); }
        .btn-danger { color: var(--danger); }
        .actions { display: flex; gap: 0.25rem; align-items: center; }
        .empty { padding: 2rem 1.1rem; text-align: center; color: var(--muted); }
        .back { color: var(--muted); text-decoration: none; font-size: 0.9rem; }
        .back:hover { color: var(--accent); }
        @media (max-width: 800px) {
            .shell { grid-template-columns: 1fr; }
            .sidebar { border-right: 0; border-bottom: 1px solid var(--line); }
            .nav { flex-direction: row; flex-wrap: wrap; }
            .main { padding: 1.25rem; }
        }
    </style>
</head>
<body>
<div class="shell">
    <aside class="sidebar">
        <p class="brand">Todo <span>Hub OTA</span></p>
        <p class="muted" style="margin:0 0 1rem;font-size:0.75rem">v{{ config('app.version') }} · OTA demo</p>
        <nav class="nav">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('spokes.index') }}" class="{{ request()->routeIs('spokes.*') ? 'active' : '' }}">Spokes</a>
            <a href="{{ route('menus.index') }}" class="{{ request()->routeIs('menus.*') ? 'active' : '' }}">Menu links</a>
        </nav>
    </aside>
    <main class="main">
        @if (session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif
        @yield('content')
    </main>
</div>
</body>
</html>
