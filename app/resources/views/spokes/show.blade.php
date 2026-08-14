@extends('layouts.app')

@section('title', $spoke->name)

@section('content')
    <div class="page-head">
        <a class="back" href="{{ route('spokes.index') }}">← All spokes</a>
        <h1 style="margin-top:0.5rem">{{ $spoke->name }}</h1>
        <p>
            <span class="muted">{{ $spoke->id }}</span>
            ·
            <span class="badge {{ $spoke->is_active ? 'ok' : 'off' }}">
                {{ $spoke->is_active ? 'Active' : 'Inactive' }}
            </span>
        </p>
    </div>

    <div class="panel">
        <div class="panel-head">Todos ({{ $todos->count() }})</div>
        @if ($todos->isEmpty())
            <div class="empty">No todos for this spoke yet.</div>
        @else
            <table>
                <thead>
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Updated</th>
                    <th>Created</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($todos as $todo)
                    <tr>
                        <td>{{ $todo->title }}</td>
                        <td>
                            <span class="badge {{ $todo->done ? 'done' : 'open' }}">
                                {{ $todo->done ? 'Done' : 'Open' }}
                            </span>
                        </td>
                        <td class="muted">{{ $todo->updated_at?->format('Y-m-d H:i') }}</td>
                        <td class="muted">{{ $todo->created_at?->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
