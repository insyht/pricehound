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
    ];

    public function urls()
    {
        return $this->hasMany(Url::class);
    }

    public function scopeMine($query)
    {
        return $query->whereHas('urls', function ($q) {
            $q->where('user_id', auth()?->id() ?? 0);
        });
    }
}
