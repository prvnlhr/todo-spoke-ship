@extends('layouts.app')

@section('title', 'Spokes')

@section('content')
    <div class="page-head">
        <h1>Spokes</h1>
        <p>Clients / installations. Click one to see their todos.</p>
    </div>

    <div class="panel">
        <div class="panel-head">All spokes</div>
        @if ($spokes->isEmpty())
            <div class="empty">No spokes yet.</div>
        @else
            <table>
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Spoke ID</th>
                    <th>Todos</th>
                    <th>Status</th>
                    <th>Last sync</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($spokes as $spoke)
                    <tr>
                        <td>
                            <a class="row-link" href="{{ route('spokes.show', $spoke) }}">{{ $spoke->name }}</a>
                        </td>
                        <td class="muted">{{ $spoke->id }}</td>
                        <td>{{ $spoke->todos_count }}</td>
                        <td>
                            <span class="badge {{ $spoke->is_active ? 'ok' : 'off' }}">
                                {{ $spoke->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="muted">
                            {{ $spoke->last_synced_at?->diffForHumans() ?? 'Never' }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
