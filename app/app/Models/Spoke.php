<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['id', 'name', 'api_token', 'is_active', 'last_synced_at'])]
class Spoke extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_synced_at' => 'datetime',
        ];
    }

    public function todos(): HasMany
    {
        return $this->hasMany(Todo::class, 'spoke_id');
    }

    public function menus(): HasMany
    {
        return $this->hasMany(UserMenu::class, 'spoke_id');
    }
}
