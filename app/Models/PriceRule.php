<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceRule extends Model
{
    protected $fillable = [
        'product_user_id',
        'type',
        'value',
    ];

    public function user()
    {
        return $this->hasOneThrough(User::class, ProductUser::class, 'id', 'id', 'product_user_id', 'user_id');

    }

    public function product()
    {
        return $this->hasOneThrough(Product::class, ProductUser::class, 'id', 'id', 'product_user_id', 'product_id');
    }
}
