<?php

namespace App\Traits;

use App\Models\Scopes\OnlyForCurrentUser;

trait BelongsToUser
{
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function scopeMine($query)
    {
        return $query->where('user_id', auth()?->id() ?? 0);
    }

    final public static function booted()
    {
        parent::booted();

        static::addGlobalScope(new OnlyForCurrentUser());
    }
}
