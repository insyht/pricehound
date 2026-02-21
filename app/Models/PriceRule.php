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
        $this->hasOneThrough(User::class, ProductUser::class);
    }
}
