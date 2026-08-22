@extends('hub.layout')

@section('title', 'Spokes')

@section('content')
    <h1>Spokes</h1>
    <p class="lede">Register each border post. IDs must match the spoke’s <code>SPOKE_ID</code> in its .env.</p>

    <div class="panel">
        <h2>Add spoke</h2>
        <form method="post" action="{{ route('spokes.store') }}" class="form-grid">
            @csrf
            <label>
                Spoke ID
                <input name="id" value="{{ old('id') }}" placeholder="post-north-01" required pattern="[a-z0-9\-]+">
            </label>
            <label>
                Display name
                <input name="name" value="{{ old('name') }}" placeholder="Border Post North" required>
            </label>
            <button type="submit">Add</button>
        </form>
    </div>

    <div class="panel">
        <h2>All spokes</h2>
        @if ($spokes->isEmpty())
            <div class="empty">No spokes registered.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>ID</th>
                        <th>Todos</th>
                        <th>Last import</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($spokes as $spoke)
                        <tr>
                            <td><a class="row-link" href="{{ route('spokes.show', $spoke) }}">{{ $spoke->name }}</a></td>
                            <td><code>{{ $spoke->id }}</code></td>
                            <td>{{ $spoke->todos_count }}</td>
                            <td>{{ $spoke->last_imported_at?->toDayDateTimeString() ?? '—' }}</td>
                            <td>
                                <form class="inline" method="post" action="{{ route('spokes.destroy', $spoke) }}" onsubmit="return confirm('Remove this spoke and its imported todos?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
