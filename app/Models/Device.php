<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A push-capable app install belonging to a user, identified by its FCM registration token.
 */
class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'platform',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
