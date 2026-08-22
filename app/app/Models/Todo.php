<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['title', 'done', 'spoke_id', 'remote_id'])]
class Todo extends Model
{
    protected function casts(): array
    {
        return [
            'done' => 'boolean',
        ];
    }

    public function spoke(): BelongsTo
    {
        return $this->belongsTo(Spoke::class, 'spoke_id');
    }
}
