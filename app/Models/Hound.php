<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hound extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'url',
        'last_ping',
        'online',
    ];

    protected function casts(): array
    {
        return [
            'last_ping' => 'datetime',
            'online' => 'boolean',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
