<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;

class Stylist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'biography',
        'specialty',
        'availability',
        'average_rating',
        'total_reviews',
        'is_available'
    ];

    protected $casts = [
        'availability' => 'array',
        'is_available' => 'boolean',
        'average_rating' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
