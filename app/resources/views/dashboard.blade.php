@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="page-head">
        <h1>Dashboard OTA</h1>
        <p>This hub text was published with the spoke image.</p>
    </div>

    <div class="cards">
        <div class="card">
            <div class="label">Spokes</div>
            <div class="value">{{ $spokeCount }}</div>
        </div>
        <div class="card">
            <div class="label">Active</div>
            <div class="value">{{ $activeSpokes }}</div>
        </div>
        <div class="card">
            <div class="label">Todos</div>
            <div class="value">{{ $todoCount }}</div>
        </div>
        <div class="card">
            <div class="label">Menu links</div>
            <div class="value">{{ $menuCount }}</div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-head">Recent todos</div>
        @if ($recentTodos->isEmpty())
            <div class="empty">No todos yet. Seed data or sync from a spoke.</div>
        @else
            <table>
                <thead>
                <tr>
                    <th>Title</th>
                    <th>Spoke</th>
                    <th>Status</th>
                    <th>Updated</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($recentTodos as $todo)
                    <tr>
                        <td>{{ $todo->title }}</td>
                        <td>
                            <a class="row-link" href="{{ route('spokes.show', $todo->spoke_id) }}">
                                {{ $todo->spoke?->name ?? $todo->spoke_id }}
                            </a>
                        </td>
                        <td>
                            <span class="badge {{ $todo->done ? 'done' : 'open' }}">
                                {{ $todo->done ? 'Done' : 'Open' }}
                            </span>
                        </td>
                        <td class="muted">{{ $todo->updated_at?->diffForHumans() }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
