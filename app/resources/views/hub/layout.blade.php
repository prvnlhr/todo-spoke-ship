<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Hub') — {{ config('app.name') }}</title>
    <style>
        :root {
            --bg: #0f1419;
            --panel: #171d25;
            --ink: #e7ecf1;
            --muted: #8b98a5;
            --line: #2a3440;
            --accent: #3d8bfd;
            --accent-ink: #061018;
            --ok: #3ecf8e;
            --danger: #f07178;
            --radius: 12px;
            --font: "Segoe UI", system-ui, -apple-system, sans-serif;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: var(--font);
            color: var(--ink);
            background:
                radial-gradient(900px 420px at 0% 0%, #1a2a3d 0%, transparent 55%),
                radial-gradient(700px 380px at 100% 0%, #1c2430 0%, transparent 50%),
                var(--bg);
        }

        .shell {
            display: grid;
            grid-template-columns: 220px 1fr;
            min-height: 100vh;
        }

        .nav {
            padding: 1.5rem 1rem;
            border-right: 1px solid var(--line);
            background: color-mix(in srgb, var(--panel) 92%, black);
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .brand {
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            margin: 0 0.5rem 1.25rem;
        }

        .brand span {
            display: block;
            font-size: 0.72rem;
            font-weight: 500;
            color: var(--muted);
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-top: 0.25rem;
        }

        .nav a {
            display: block;
            padding: 0.65rem 0.75rem;
            border-radius: 10px;
            color: var(--muted);
            text-decoration: none;
            font-weight: 500;
        }

        .nav a:hover { color: var(--ink); background: color-mix(in srgb, white 5%, transparent); }
        .nav a.active { color: var(--ink); background: color-mix(in srgb, var(--accent) 18%, transparent); }

        .nav .meta {
            margin-top: auto;
            padding: 0.75rem 0.5rem 0;
            font-size: 0.75rem;
            color: var(--muted);
        }

        .main { min-width: 0; padding: 2rem 1.5rem 3rem; }

        .wrap { width: min(920px, 100%); margin: 0 auto; }

        h1 {
            margin: 0 0 0.35rem;
            font-size: clamp(1.6rem, 3vw, 2rem);
            letter-spacing: -0.03em;
        }

        .lede { margin: 0 0 1.5rem; color: var(--muted); }

        .flash {
            margin-bottom: 1rem;
            padding: 0.75rem 0.9rem;
            border-radius: var(--radius);
            background: color-mix(in srgb, var(--ok) 16%, transparent);
            color: #b8f0d4;
            font-size: 0.92rem;
        }

        .errors {
            margin-bottom: 1rem;
            padding: 0.75rem 0.9rem;
            border-radius: var(--radius);
            background: color-mix(in srgb, var(--danger) 16%, transparent);
            color: #ffc4c7;
            font-size: 0.92rem;
        }

        .errors ul { margin: 0; padding-left: 1.1rem; }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 1rem 1.1rem;
        }

        .card .label { color: var(--muted); font-size: 0.8rem; margin-bottom: 0.35rem; }
        .card .value { font-size: 1.75rem; font-weight: 700; letter-spacing: -0.03em; }

        .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: calc(var(--radius) + 2px);
            padding: 1.1rem;
            margin-bottom: 1.25rem;
        }

        .panel h2 {
            margin: 0 0 0.85rem;
            font-size: 1rem;
            font-weight: 650;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.92rem;
        }

        th, td {
            text-align: left;
            padding: 0.7rem 0.5rem;
            border-bottom: 1px solid var(--line);
            vertical-align: top;
        }

        th { color: var(--muted); font-weight: 560; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.04em; }

        a.row-link { color: var(--accent); text-decoration: none; }
        a.row-link:hover { text-decoration: underline; }

        .badge {
            display: inline-block;
            padding: 0.15rem 0.45rem;
            border-radius: 999px;
            font-size: 0.75rem;
            background: color-mix(in srgb, var(--line) 80%, white);
            color: var(--muted);
        }

        .badge.done {
            background: color-mix(in srgb, var(--ok) 20%, transparent);
            color: #9eecc8;
        }

        form.inline { display: inline; }

        .form-grid {
            display: grid;
            gap: 0.75rem;
            grid-template-columns: 1fr 1fr auto;
            align-items: end;
        }

        label {
            display: grid;
            gap: 0.35rem;
            font-size: 0.82rem;
            color: var(--muted);
        }

        input, select {
            width: 100%;
            border: 1px solid var(--line);
            background: #10151c;
            color: var(--ink);
            border-radius: 10px;
            padding: 0.7rem 0.8rem;
            font: inherit;
        }

        button, .btn {
            font: inherit;
            cursor: pointer;
            border: 0;
            border-radius: 10px;
            padding: 0.7rem 1rem;
            background: var(--accent);
            color: var(--accent-ink);
            font-weight: 650;
            text-decoration: none;
            display: inline-block;
        }

        button.ghost, .btn.ghost {
            background: transparent;
            color: var(--muted);
            border: 1px solid var(--line);
        }

        button.danger {
            background: transparent;
            color: var(--danger);
            border: 1px solid color-mix(in srgb, var(--danger) 40%, var(--line));
            padding: 0.35rem 0.6rem;
            font-size: 0.8rem;
            font-weight: 560;
        }

        .actions { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1rem; }

        .empty {
            color: var(--muted);
            text-align: center;
            padding: 1.5rem;
            border: 1px dashed var(--line);
            border-radius: var(--radius);
        }

        @media (max-width: 720px) {
            .shell { grid-template-columns: 1fr; }
            .nav { flex-direction: row; flex-wrap: wrap; border-right: 0; border-bottom: 1px solid var(--line); }
            .brand { width: 100%; margin-bottom: 0.5rem; }
            .nav .meta { display: none; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <aside class="nav" aria-label="Hub">
            <div class="brand">
                {{ config('app.name') }}
                <span>Offline hub</span>
            </div>
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('spokes.index') }}" class="{{ request()->routeIs('spokes.*') ? 'active' : '' }}">Spokes</a>
            <a href="{{ route('import.create') }}" class="{{ request()->routeIs('import.*') ? 'active' : '' }}">Import</a>
            <div class="meta">v{{ config('app.version') }} · local only</div>
        </aside>
        <div class="main">
            <div class="wrap">
                @if (session('status'))
                    <div class="flash">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="errors">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
