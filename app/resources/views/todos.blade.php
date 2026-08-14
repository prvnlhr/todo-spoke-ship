<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0f1c18;
            --bg-elevated: #162821;
            --ink: #e8f0ec;
            --muted: #8aa396;
            --line: #2a4037;
            --accent: #3ecf8e;
            --accent-ink: #062216;
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
            background:
                radial-gradient(1200px 600px at 10% -10%, #1d3d32 0%, transparent 55%),
                radial-gradient(900px 500px at 100% 0%, #243528 0%, transparent 50%),
                var(--bg);
        }

        .wrap {
            width: min(560px, calc(100% - 2rem));
            margin: 0 auto;
            padding: 3rem 0 4rem;
        }

        .shell {
            display: grid;
            grid-template-columns: 1fr;
            min-height: 100vh;
        }

        .shell.has-nav {
            grid-template-columns: 220px 1fr;
        }

        .nav {
            display: none;
            padding: 2rem 1rem;
            border-right: 1px solid var(--line);
            background: color-mix(in srgb, var(--bg-elevated) 90%, black);
        }

        .shell.has-nav .nav {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .nav-title {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
            margin: 0 0 0.75rem 0.5rem;
        }

        .nav a {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 0.75rem;
            border-radius: 10px;
            color: var(--muted);
            text-decoration: none;
            font-weight: 500;
        }

        .nav a:hover {
            color: var(--ink);
            background: color-mix(in srgb, white 5%, transparent);
        }

        .nav-icon {
            width: 1.25rem;
            text-align: center;
            font-size: 0.75rem;
            color: var(--accent);
            text-transform: uppercase;
        }

        .version {
            margin-top: auto;
            padding: 0.75rem 0.5rem 0;
            font-size: 0.75rem;
            color: var(--muted);
        }

        .main { min-width: 0; }

        header {
            margin-bottom: 2rem;
            animation: rise 0.5s ease both;
        }

        .brand {
            font-family: var(--display);
            font-size: clamp(2rem, 6vw, 2.75rem);
            font-weight: 700;
            letter-spacing: -0.03em;
            line-height: 1.1;
            margin: 0 0 0.5rem;
        }

        .lede {
            margin: 0;
            color: var(--muted);
            font-size: 1rem;
            line-height: 1.5;
        }

        .meta {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            margin-top: 0.85rem;
            font-size: 0.8rem;
            color: var(--muted);
        }

        .dot {
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 50%;
            background: var(--accent);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent) 25%, transparent);
        }

        .composer {
            display: flex;
            gap: 0.65rem;
            margin-bottom: 1.25rem;
            animation: rise 0.55s ease 0.05s both;
        }

        .composer input {
            flex: 1;
            min-width: 0;
            border: 1px solid var(--line);
            background: color-mix(in srgb, var(--bg-elevated) 88%, black);
            color: var(--ink);
            border-radius: var(--radius);
            padding: 0.9rem 1rem;
            font: inherit;
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .composer input:focus {
            border-color: color-mix(in srgb, var(--accent) 70%, var(--line));
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent) 18%, transparent);
        }

        .composer button,
        .todo-actions button {
            font: inherit;
            cursor: pointer;
            border: 0;
        }

        .composer button {
            background: var(--accent);
            color: var(--accent-ink);
            font-weight: 600;
            border-radius: var(--radius);
            padding: 0 1.15rem;
            transition: transform 0.12s ease, filter 0.12s ease;
        }

        .composer button:hover { filter: brightness(1.05); }
        .composer button:active { transform: translateY(1px); }
        .composer button:disabled { opacity: 0.55; cursor: wait; }

        .list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 0.55rem;
            animation: rise 0.6s ease 0.1s both;
        }

        .todo {
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 0.75rem;
            padding: 0.85rem 0.95rem;
            background: color-mix(in srgb, var(--bg-elevated) 92%, black);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            transition: opacity 0.2s ease, transform 0.2s ease, border-color 0.15s ease;
        }

        .todo.done {
            opacity: 0.62;
        }

        .todo.done .title {
            text-decoration: line-through;
            color: var(--muted);
        }

        .todo.removing {
            opacity: 0;
            transform: translateX(8px);
        }

        .check {
            width: 1.25rem;
            height: 1.25rem;
            border-radius: 0.35rem;
            border: 1.5px solid var(--line);
            background: transparent;
            display: grid;
            place-items: center;
            color: transparent;
            transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease;
        }

        .todo.done .check {
            background: var(--accent);
            border-color: var(--accent);
            color: var(--accent-ink);
        }

        .title {
            margin: 0;
            font-size: 0.98rem;
            line-height: 1.35;
            word-break: break-word;
        }

        .title[contenteditable="true"] {
            outline: none;
            border-bottom: 1px dashed color-mix(in srgb, var(--accent) 50%, transparent);
        }

        .todo-actions {
            display: flex;
            gap: 0.25rem;
        }

        .todo-actions button {
            background: transparent;
            color: var(--muted);
            padding: 0.35rem 0.45rem;
            border-radius: 0.4rem;
            font-size: 0.8rem;
        }

        .todo-actions button:hover {
            color: var(--ink);
            background: color-mix(in srgb, white 6%, transparent);
        }

        .todo-actions .delete:hover {
            color: var(--danger);
        }

        .empty {
            text-align: center;
            color: var(--muted);
            padding: 2.5rem 1rem;
            border: 1px dashed var(--line);
            border-radius: var(--radius);
        }

        .error {
            display: none;
            margin-bottom: 1rem;
            padding: 0.75rem 0.9rem;
            border-radius: var(--radius);
            background: color-mix(in srgb, var(--danger) 15%, transparent);
            color: #ffc4c7;
            font-size: 0.9rem;
        }

        .error.show { display: block; }

        @keyframes rise {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 480px) {
            .wrap { padding-top: 2rem; }
            .composer { flex-direction: column; }
            .composer button { padding: 0.85rem 1rem; }
            .shell.has-nav { grid-template-columns: 1fr; }
            .shell.has-nav .nav {
                display: flex;
                flex-direction: row;
                flex-wrap: wrap;
                border-right: 0;
                border-bottom: 1px solid var(--line);
            }
        }
    </style>
</head>
<body>
    <div class="shell {{ $menuItems->isNotEmpty() ? 'has-nav' : '' }}">
        @if ($menuItems->isNotEmpty())
            <aside class="nav" aria-label="Menu">
                <p class="nav-title">MENU 1.5</p>
                @foreach ($menuItems as $item)
                    <a href="{{ $item->href }}">
                        @if ($item->icon)
                            <span class="nav-icon">{{ $item->icon }}</span>
                        @endif
                        <span>{{ $item->label }}</span>
                    </a>
                @endforeach
                <div class="version">v{{ config('app.version') }}</div>
            </aside>
        @endif
        <div class="main">
    <div class="wrap">
        <header>
            <h1 class="brand">{{ config('app.name') }} 1.5</h1>
            <p class="lede">Spoke UI updated via OTA. Local todos stay here until sync is online.</p>
            @if ($spokeId)
                <div class="meta"><span class="dot" aria-hidden="true"></span> {{ $spokeId }}</div>
            @endif
        </header>

        <div id="error" class="error" role="alert"></div>

        <form class="composer" id="composer">
            <input
                id="title"
                name="title"
                type="text"
                maxlength="255"
                placeholder="Add a todo…"
                autocomplete="off"
                required
            >
            <button type="submit">Add</button>
        </form>

        <ul class="list" id="list" aria-live="polite">
            @forelse ($todos as $todo)
                <li class="todo {{ $todo->done ? 'done' : '' }}" data-id="{{ $todo->id }}">
                    <button type="button" class="check" data-action="toggle" aria-label="Toggle done">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                            <path d="M2.5 6.2 4.8 8.5 9.5 3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <p class="title">{{ $todo->title }}</p>
                    <div class="todo-actions">
                        <button type="button" data-action="edit">Edit</button>
                        <button type="button" class="delete" data-action="delete">Delete</button>
                    </div>
                </li>
            @empty
                <li class="empty" id="empty">No todos yet. Add one above.</li>
            @endforelse
        </ul>
        @if ($menuItems->isEmpty())
            <div class="version" style="margin-top:1.5rem;color:var(--muted);font-size:0.75rem">v{{ config('app.version') }}</div>
        @endif
    </div>
        </div>
    </div>

    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const list = document.getElementById('list');
        const composer = document.getElementById('composer');
        const titleInput = document.getElementById('title');
        const errorBox = document.getElementById('error');

        function showError(message) {
            errorBox.textContent = message || 'Something went wrong.';
            errorBox.classList.add('show');
        }

        function clearError() {
            errorBox.classList.remove('show');
            errorBox.textContent = '';
        }

        function ensureEmptyState() {
            const hasTodos = list.querySelector('.todo');
            const empty = document.getElementById('empty');
            if (!hasTodos && !empty) {
                const li = document.createElement('li');
                li.className = 'empty';
                li.id = 'empty';
                li.textContent = 'No todos yet. Add one above.';
                list.appendChild(li);
            }
            if (hasTodos && empty) empty.remove();
        }

        function todoItem(todo) {
            const li = document.createElement('li');
            li.className = 'todo' + (todo.done ? ' done' : '');
            li.dataset.id = todo.id;
            li.innerHTML = `
                <button type="button" class="check" data-action="toggle" aria-label="Toggle done">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                        <path d="M2.5 6.2 4.8 8.5 9.5 3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <p class="title"></p>
                <div class="todo-actions">
                    <button type="button" data-action="edit">Edit</button>
                    <button type="button" class="delete" data-action="delete">Delete</button>
                </div>
            `;
            li.querySelector('.title').textContent = todo.title;
            return li;
        }

        async function api(url, options = {}) {
            const res = await fetch(url, {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                ...options,
            });

            if (!res.ok) {
                let message = 'Request failed.';
                try {
                    const body = await res.json();
                    message = body.message || Object.values(body.errors || {}).flat()[0] || message;
                } catch (_) {}
                throw new Error(message);
            }

            if (res.status === 204) return null;
            return res.json();
        }

        composer.addEventListener('submit', async (e) => {
            e.preventDefault();
            clearError();
            const title = titleInput.value.trim();
            if (!title) return;

            const btn = composer.querySelector('button');
            btn.disabled = true;

            try {
                const { todo } = await api('/todos', {
                    method: 'POST',
                    body: JSON.stringify({ title }),
                });
                list.prepend(todoItem(todo));
                titleInput.value = '';
                ensureEmptyState();
            } catch (err) {
                showError(err.message);
            } finally {
                btn.disabled = false;
                titleInput.focus();
            }
        });

        list.addEventListener('click', async (e) => {
            const actionBtn = e.target.closest('[data-action]');
            if (!actionBtn) return;

            const item = actionBtn.closest('.todo');
            if (!item) return;

            const id = item.dataset.id;
            const action = actionBtn.dataset.action;
            clearError();

            try {
                if (action === 'toggle') {
                    const done = !item.classList.contains('done');
                    const { todo } = await api(`/todos/${id}`, {
                        method: 'PATCH',
                        body: JSON.stringify({ done }),
                    });
                    item.classList.toggle('done', todo.done);
                }

                if (action === 'delete') {
                    await api(`/todos/${id}`, { method: 'DELETE' });
                    item.classList.add('removing');
                    setTimeout(() => {
                        item.remove();
                        ensureEmptyState();
                    }, 180);
                }

                if (action === 'edit') {
                    const titleEl = item.querySelector('.title');
                    if (titleEl.isContentEditable) return;
                    titleEl.contentEditable = 'true';
                    titleEl.focus();

                    const range = document.createRange();
                    range.selectNodeContents(titleEl);
                    const sel = window.getSelection();
                    sel.removeAllRanges();
                    sel.addRange(range);

                    const finish = async (save) => {
                        titleEl.contentEditable = 'false';
                        titleEl.removeEventListener('blur', onBlur);
                        titleEl.removeEventListener('keydown', onKey);
                        if (!save) return;

                        const title = titleEl.textContent.trim();
                        if (!title) {
                            showError('Title cannot be empty.');
                            return;
                        }

                        try {
                            const { todo } = await api(`/todos/${id}`, {
                                method: 'PATCH',
                                body: JSON.stringify({ title }),
                            });
                            titleEl.textContent = todo.title;
                        } catch (err) {
                            showError(err.message);
                        }
                    };

                    const onBlur = () => finish(true);
                    const onKey = (ev) => {
                        if (ev.key === 'Enter') {
                            ev.preventDefault();
                            titleEl.blur();
                        }
                        if (ev.key === 'Escape') {
                            finish(false);
                        }
                    };

                    titleEl.addEventListener('blur', onBlur);
                    titleEl.addEventListener('keydown', onKey);
                }
            } catch (err) {
                showError(err.message);
            }
        });
    </script>
</body>
</html>
