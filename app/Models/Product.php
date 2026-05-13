<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'identifier',
        'created_by_user_id',
    ];

    protected $appends = ['lowest_price'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function prices()
    {
        return $this->hasMany(Price::class)->orderByDesc('created_at');
    }

    public function getLowestPriceAttribute(): ?Price
    {
        $user = auth()->user();
        return $this->prices()
                  ->where('hound_id', $user->hound_id)
                  ->where('user_id', $user->id)
            ->orderByDesc('fetched_at')
            ->first();
    }
}
