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

        // When prices are already eager-loaded (e.g. the index/show endpoints call
        // ->load('prices')), filter the in-memory collection to avoid an N+1 query per product.
        if ($this->relationLoaded('prices')) {
            return $this->prices
                ->where('hound_id', $user->hound_id)
                ->where('user_id', $user->id)
                ->sortByDesc('fetched_at')
                ->first();
        }

        return $this->prices()
                  ->where('hound_id', $user->hound_id)
                  ->where('user_id', $user->id)
            ->orderByDesc('fetched_at')
            ->first();
    }
}
