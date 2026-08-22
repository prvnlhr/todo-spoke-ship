<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <style>
        :root {
            --bg: #f4f1ea;
            --paper: #fffdf8;
            --ink: #1c1917;
            --muted: #78716c;
            --line: #e7e0d6;
            --accent: #0f766e;
            --accent-ink: #f0fdfa;
            --danger: #b91c1c;
            --shadow: 0 18px 40px rgba(28, 25, 23, 0.08);
            --radius: 16px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", system-ui, -apple-system, sans-serif;
            color: var(--ink);
            background:
                radial-gradient(900px 420px at 0% 0%, #d7efe9 0%, transparent 60%),
                radial-gradient(700px 380px at 100% 10%, #f3e6d4 0%, transparent 55%),
                var(--bg);
        }

        .wrap {
            width: min(640px, calc(100% - 2rem));
            margin: 0 auto;
            padding: 3.5rem 0 4rem;
        }

        header { margin-bottom: 1.75rem; }

        h1 {
            margin: 0 0 0.4rem;
            font-size: clamp(2rem, 5vw, 2.6rem);
            letter-spacing: -0.04em;
            line-height: 1.1;
        }

        .lede {
            margin: 0;
            color: var(--muted);
            font-size: 1.02rem;
        }

        .card {
            background: var(--paper);
            border: 1px solid var(--line);
            border-radius: calc(var(--radius) + 4px);
            box-shadow: var(--shadow);
            padding: 1.1rem;
        }

        .composer {
            display: flex;
            gap: 0.65rem;
            margin-bottom: 1rem;
        }

        .composer input {
            flex: 1;
            min-width: 0;
            border: 1px solid var(--line);
            background: #fff;
            color: var(--ink);
            border-radius: 12px;
            padding: 0.9rem 1rem;
            font: inherit;
            outline: none;
        }

        .composer input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.16);
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
            font-weight: 650;
            border-radius: 12px;
            padding: 0 1.2rem;
        }

        .composer button:disabled { opacity: 0.55; cursor: wait; }

        .list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .todo {
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 0.75rem;
            padding: 0.8rem 0.85rem;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 12px;
        }

        .todo.done { opacity: 0.62; }
        .todo.done .title {
            text-decoration: line-through;
            color: var(--muted);
        }
        .todo.removing { opacity: 0; }

        .check {
            width: 1.3rem;
            height: 1.3rem;
            border-radius: 0.4rem;
            border: 1.5px solid var(--line);
            background: transparent;
            display: grid;
            place-items: center;
            color: transparent;
            padding: 0;
            cursor: pointer;
        }

        .todo.done .check {
            background: var(--accent);
            border-color: var(--accent);
            color: var(--accent-ink);
        }

        .title {
            margin: 0;
            font-size: 0.98rem;
            line-height: 1.4;
            word-break: break-word;
        }

        .title[contenteditable="true"] {
            outline: none;
            border-bottom: 1px dashed var(--accent);
        }

        .todo-actions { display: flex; gap: 0.15rem; }

        .todo-actions button {
            background: transparent;
            color: var(--muted);
            padding: 0.35rem 0.5rem;
            border-radius: 0.4rem;
            font-size: 0.82rem;
        }

        .todo-actions button:hover {
            color: var(--ink);
            background: #f5f0e8;
        }

        .todo-actions .delete:hover { color: var(--danger); }

        .empty {
            text-align: center;
            color: var(--muted);
            padding: 2.2rem 1rem;
            border: 1px dashed var(--line);
            border-radius: 12px;
        }

        .error {
            display: none;
            margin-bottom: 0.85rem;
            padding: 0.7rem 0.85rem;
            border-radius: 12px;
            background: #fee2e2;
            color: var(--danger);
            font-size: 0.9rem;
        }

        .error.show { display: block; }

        .version {
            margin-top: 1.25rem;
            text-align: center;
            color: var(--muted);
            font-size: 0.78rem;
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .toolbar .meta {
            font-size: 0.82rem;
            color: var(--muted);
        }

        .toolbar a {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--accent);
            text-decoration: none;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 0.45rem 0.75rem;
            background: #fff;
        }

        .toolbar a:hover { border-color: var(--accent); }

        @media (max-width: 520px) {
            .wrap { padding-top: 2rem; }
            .composer { flex-direction: column; }
            .composer button { padding: 0.85rem 1rem; }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <header>
            <h1>{{ config('app.name') }}</h1>
            <p class="lede">Add, edit, complete, and delete todos. Saved in MySQL on this machine.</p>
        </header>

        <div class="toolbar">
            <div class="meta">@if (!empty($spokeId)) Spoke <strong>{{ $spokeId }}</strong>@else Local spoke @endif</div>
            <a href="{{ route('todos.export') }}">Export for hub (USB)</a>
        </div>

        <div class="card">
            <div id="error" class="error" role="alert"></div>

            <form class="composer" id="composer">
                <input id="title" name="title" type="text" maxlength="255" placeholder="Add a todo…" autocomplete="off" required>
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
        </div>
        <div class="version">v{{ config('app.version') }} · local</div>
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
                    }, 160);
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
                        if (ev.key === 'Escape') finish(false);
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
