@extends('layouts.app')

@section('title', 'Menu links')

@section('content')
    <div class="page-head">
        <h1>Menu links</h1>
        <p>Sidebar links pushed to spokes. Leave spoke empty for all clients.</p>
    </div>

    <div class="panel">
        <div class="panel-head">Add link</div>
        <form method="post" action="{{ route('menus.store') }}" class="form-grid">
            @csrf
            <label>
                Label
                <input type="text" name="label" value="{{ old('label') }}" required maxlength="255">
            </label>
            <label>
                Href
                <input type="text" name="href" value="{{ old('href', '/') }}" required maxlength="255">
            </label>
            <label>
                Icon
                <input type="text" name="icon" value="{{ old('icon') }}" maxlength="50" placeholder="optional">
            </label>
            <label>
                Spoke
                <select name="spoke_id">
                    <option value="">All spokes</option>
                    @foreach ($spokes as $spoke)
                        <option value="{{ $spoke->id }}" @selected(old('spoke_id') === $spoke->id)>
                            {{ $spoke->name }}
                        </option>
                    @endforeach
                </select>
            </label>
            <label>
                Sort
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
            </label>
            <label style="justify-content:end">
                &nbsp;
                <button type="submit" class="btn btn-primary">Add link</button>
            </label>
        </form>

        @if ($menus->isEmpty())
            <div class="empty">No menu links yet.</div>
        @else
            <table>
                <thead>
                <tr>
                    <th>Label</th>
                    <th>Href</th>
                    <th>Icon</th>
                    <th>Scope</th>
                    <th>Sort</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($menus as $menu)
                    <tr>
                        <td colspan="6" style="padding:0">
                            <form method="post" action="{{ route('menus.update', $menu) }}" class="form-grid" style="border-bottom:1px solid var(--line); margin:0">
                                @csrf
                                @method('PATCH')
                                <label>
                                    Label
                                    <input type="text" name="label" value="{{ $menu->label }}" required>
                                </label>
                                <label>
                                    Href
                                    <input type="text" name="href" value="{{ $menu->href }}" required>
                                </label>
                                <label>
                                    Icon
                                    <input type="text" name="icon" value="{{ $menu->icon }}">
                                </label>
                                <label>
                                    Spoke
                                    <select name="spoke_id">
                                        <option value="">All spokes</option>
                                        @foreach ($spokes as $spoke)
                                            <option value="{{ $spoke->id }}" @selected($menu->spoke_id === $spoke->id)>
                                                {{ $spoke->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </label>
                                <label>
                                    Sort
                                    <input type="number" name="sort_order" value="{{ $menu->sort_order }}" min="0">
                                </label>
                                <div class="actions" style="align-items:end; padding-bottom:0.15rem">
                                    <button type="submit" class="btn btn-ghost">Save</button>
                                </div>
                            </form>
                            <form method="post" action="{{ route('menus.destroy', $menu) }}" style="padding:0 1.1rem 0.9rem; margin-top:-0.4rem" onsubmit="return confirm('Delete this link?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
