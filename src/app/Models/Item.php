<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Favorite;
use App\Models\Comment;
use App\Models\Category;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'description', 'price', 'condition', 'image_path','img_url','is_sold',
    ];

    protected $casts = [
        'price'   => 'int',
        'is_sold' => 'boolean',
    ];

    public function user()      { return $this->belongsTo(User::class); }
    public function favorites() { return $this->hasMany(Favorite::class); }
    public function comments()  { return $this->hasMany(Comment::class); }
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_item', 'item_id', 'category_id');
    }
    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class, 'item_id');
    }

    public function isLikedBy($user): bool
    {
        if (is_null($user)) return false;
        return $this->favorites()->where('user_id', $user->id)->exists();
    }

    public function getConditionLabelAttribute()
    {
    return self::$conditionLabels[$this->condition] ?? '不明';
    }

    public function getImageUrlAttribute(): string
    {
        $path = $this->image_path ?? $this->img_url ?? $this->image ?? null;
        if (!$path) return asset('images/no-image.png');
        if (preg_match('~^https?://~', $path)) return $path;

        $path = ltrim($path, '/');
        if (str_starts_with($path, 'public/'))  $path = substr($path, 7);
        if (str_starts_with($path, 'storage/')) return '/'.$path;

        return Storage::url($path);
    }

    public function getIsSoldComputedAttribute(): bool
    {
        return $this->orders()->exists();
    }

    public function scopeAvailable($q) { return $q->where('is_sold', false); }
    public function scopeSold($q)      { return $q->where('is_sold', true); }

    public static $conditionLabels = [
        1 => '新品・未使用',
        2 => '未使用に近い',
        3 => '目立った傷や汚れなし',
        4 => 'やや傷や汚れあり',
        5 => '傷や汚れあり',
        6 => '全体的に状態が悪い',
    ];

}
