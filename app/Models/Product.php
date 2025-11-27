<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'ean',
        'created_by_user_id',
    ];

    public function urls()
    {
        return $this->hasMany(Url::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function shops()
    {
        return $this->hasMany(Shop::class);
    }

    public function prices()
    {
        return $this->hasManyThrough(Price::class, ProductShop::class)->orderByDesc('created_at');
    }
}
