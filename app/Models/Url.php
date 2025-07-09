<?php

namespace App\Models;

use App\Interfaces\BelongsToUserInterface;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Url extends Model implements BelongsToUserInterface
{
    use BelongsToUser;
    use HasFactory;

    protected $fillable = [
        'url',
        'product_id',
        'shop_id',
        'user_id'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->user_id = auth()?->id();
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
