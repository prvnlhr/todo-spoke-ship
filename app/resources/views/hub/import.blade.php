@extends('hub.layout')

@section('title', 'Import')

@section('content')
    <h1>Import spoke data</h1>
    <p class="lede">Choose a spoke, then upload the JSON file exported from that border post (USB / zip).</p>

    @if ($spokes->isEmpty())
        <div class="panel">
            <div class="empty">Add a spoke first on the <a class="row-link" href="{{ route('spokes.index') }}">Spokes</a> page.</div>
        </div>
    @else
        <div class="panel">
            <form method="post" action="{{ route('import.store') }}" enctype="multipart/form-data">
                @csrf
                <div style="display:grid;gap:0.85rem;max-width:420px">
                    <label>
                        Spoke
                        <select name="spoke_id" required>
                            <option value="">Select…</option>
                            @foreach ($spokes as $spoke)
                                <option value="{{ $spoke->id }}" @selected(old('spoke_id', request('spoke_id')) === $spoke->id)>
                                    {{ $spoke->name }} ({{ $spoke->id }})
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        Export file (.json)
                        <input type="file" name="file" accept=".json,application/json" required>
                    </label>
                    <button type="submit">Import</button>
                </div>
            </form>
        </div>
    @endif
@endsection
