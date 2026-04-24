<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable =
        [
            'store_id',
            'user_id',
            'chair_id',
            'total_amount',
            'expires_at',
        ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public const EXPIRATION_MINUTES = 30;

    public function scopeActive($query)
    {
        return $query->whereDoesntHave('orders')
            ->whereHas('cartMenus')
            ->where('expires_at', '>', now());
    }

    public static function getActiveOrCreateForChair(Chair $chair): self
    {
        $cart = $chair->carts()
            ->whereDoesntHave('orders')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->first();

        if (! $cart) {
            $cart = $chair->carts()->create([
                'store_id' => $chair->store_id,
                'expires_at' => now()->addMinutes(self::EXPIRATION_MINUTES),
            ]);
        }

        return $cart;
    }

    public function bumpExpiration(): void
    {
        $this->update(['expires_at' => now()->addMinutes(self::EXPIRATION_MINUTES)]);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chair()
    {
        return $this->belongsTo(Chair::class);
    }

    public function cartMenus()
    {
        return $this->hasMany(CartMenu::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
