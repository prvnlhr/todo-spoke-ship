<?php

namespace App\Http\Controllers;

use App\Models\Spoke;
use App\Models\UserMenu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserMenuController extends Controller
{
    public function index(): View
    {
        $menus = UserMenu::query()
            ->with('spoke')
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get();

        $spokes = Spoke::query()->orderBy('name')->get();

        return view('menus.index', compact('menus', 'spokes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'href' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'spoke_id' => ['nullable', 'string', 'exists:spokes,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        UserMenu::query()->create([
            'label' => trim($data['label']),
            'href' => trim($data['href']),
            'icon' => isset($data['icon']) ? trim($data['icon']) : null,
            'spoke_id' => $data['spoke_id'] ?: null,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return redirect()->route('menus.index')->with('status', 'Menu link created.');
    }

    public function update(Request $request, UserMenu $menu): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'href' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'spoke_id' => ['nullable', 'string', 'exists:spokes,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $menu->update([
            'label' => trim($data['label']),
            'href' => trim($data['href']),
            'icon' => isset($data['icon']) ? trim($data['icon']) : null,
            'spoke_id' => $data['spoke_id'] ?: null,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return redirect()->route('menus.index')->with('status', 'Menu link updated.');
    }

    public function destroy(UserMenu $menu): RedirectResponse
    {
        $menu->delete();

        return redirect()->route('menus.index')->with('status', 'Menu link deleted.');
    }
}
