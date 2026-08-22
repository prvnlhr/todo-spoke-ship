@extends('hub.layout')

@section('title', 'Dashboard')

@section('content')
    <h1>Dashboard</h1>
    <p class="lede">Office hub — pull spoke data from USB JSON files. No internet required.</p>

    <div class="cards">
        <div class="card">
            <div class="label">Spokes</div>
            <div class="value">{{ $spokeCount }}</div>
        </div>
        <div class="card">
            <div class="label">Imported todos</div>
            <div class="value">{{ $todoCount }}</div>
        </div>
    </div>

    <div class="actions">
        <a class="btn" href="{{ route('import.create') }}">Import from USB</a>
        <a class="btn ghost" href="{{ route('spokes.index') }}">Manage spokes</a>
    </div>

    <div class="panel">
        <h2>Recent spokes</h2>
        @if ($recentSpokes->isEmpty())
            <div class="empty">No spokes yet. Add a border post, then import its export file.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Spoke</th>
                        <th>ID</th>
                        <th>Last import</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentSpokes as $spoke)
                        <tr>
                            <td><a class="row-link" href="{{ route('spokes.show', $spoke) }}">{{ $spoke->name }}</a></td>
                            <td><code>{{ $spoke->id }}</code></td>
                            <td>{{ $spoke->last_imported_at?->diffForHumans() ?? 'Never' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
