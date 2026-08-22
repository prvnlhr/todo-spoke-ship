<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['id', 'name', 'last_imported_at'])]
class Spoke extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected function casts(): array
    {
        return [
            'last_imported_at' => 'datetime',
        ];
    }

    public function todos(): HasMany
    {
        return $this->hasMany(Todo::class, 'spoke_id');
    }
}
