<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $fillable = [
        'request_id',
        'hound_id',
        'url',
        'headers',
        'callback_url',
    ];

    protected function casts(): array
    {
        return [
            'headers' => 'array',
        ];
    }

    public function hound()
    {
        return $this->belongsTo(Hound::class);
    }
}
