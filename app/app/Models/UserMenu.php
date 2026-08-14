<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['label', 'href', 'icon', 'spoke_id', 'sort_order'])]
class UserMenu extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function spoke(): BelongsTo
    {
        return $this->belongsTo(Spoke::class, 'spoke_id');
    }

    public function scopeForSpoke(Builder $query, ?string $spokeId): Builder
    {
        return $query->where(function (Builder $q) use ($spokeId) {
            $q->whereNull('spoke_id');
            if ($spokeId) {
                $q->orWhere('spoke_id', $spokeId);
            }
        });
    }
}
