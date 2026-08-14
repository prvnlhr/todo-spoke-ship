<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['title', 'done', 'spoke_id', 'synced_at'])]
class Todo extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'done' => 'boolean',
            'synced_at' => 'datetime',
        ];
    }

    public function spoke(): BelongsTo
    {
        return $this->belongsTo(Spoke::class, 'spoke_id');
    }
}
