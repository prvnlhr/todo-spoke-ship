@extends('hub.layout')

@section('title', $spoke->name)

@section('content')
    <h1>{{ $spoke->name }}</h1>
    <p class="lede">
        <code>{{ $spoke->id }}</code>
        · Last import: {{ $spoke->last_imported_at?->toDayDateTimeString() ?? 'never' }}
    </p>

    <div class="actions">
        <a class="btn" href="{{ route('import.create', ['spoke_id' => $spoke->id]) }}">Import for this spoke</a>
        <a class="btn ghost" href="{{ route('spokes.index') }}">Back to spokes</a>
    </div>

    <div class="panel">
        <h2>Todos ({{ $todos->count() }})</h2>
        @if ($todos->isEmpty())
            <div class="empty">No imported todos yet. Export from the spoke, copy the JSON to USB, then import here.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Remote ID</th>
                        <th>Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($todos as $todo)
                        <tr>
                            <td>{{ $todo->title }}</td>
                            <td>
                                @if ($todo->done)
                                    <span class="badge done">Done</span>
                                @else
                                    <span class="badge">Open</span>
                                @endif
                            </td>
                            <td><code>{{ $todo->remote_id }}</code></td>
                            <td>{{ $todo->updated_at?->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
