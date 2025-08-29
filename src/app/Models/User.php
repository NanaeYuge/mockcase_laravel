<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'postal_code',
        'address',
        'building',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // 出品した商品（1対多）
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    // 購入した注文（1対多）
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function favorites()
    {
    return $this->hasMany(Favorite::class);
    }

public function getProfileImagePathAttribute()
{
    if (!$this->profile_image) {
        return asset('images/default-user.png'); // デフォルト画像
    }

    if (str_starts_with($this->profile_image, 'http')) {
        return $this->profile_image;
    }

    return asset('storage/' . $this->profile_image);
}

}
