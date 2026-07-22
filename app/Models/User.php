<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'hound_id',
        'hound_api_key',
        'next_fetch',
        'fetch_interval',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'next_fetch' => 'datetime',
            'fetch_interval' => 'integer',
        ];
    }

    protected static function booted()
    {
        parent::booted();

        static::saving(function ($user) {
            if ($user->hound_id === null) {
                $user->hound_id = Hound::where('name', 'Pricehound default')->first()->id;
            }
        });
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('checked_at');
    }

    public function hound()
    {
        return $this->belongsTo(Hound::class);
    }

    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    public function priceRules()
    {
        return $this->hasManyThrough(PriceRule::class, ProductUser::class);
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }
}
