<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Stylist;
use App\Models\Order;
use App\Models\Review;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'stylist_id',
        'name',
        'description',
        'price',
        'categories',
        'estimated_delivery_days',
        'photos',
        'materials',
        'is_available',
        'views',
        'average_rating',
        'total_reviews'
    ];

    protected $casts = [
        'categories' => 'array',
        'photos' => 'array',
        'materials' => 'array',
        'is_available' => 'boolean',
        'price' => 'decimal:2',
        'average_rating' => 'decimal:2',
    ];

    public function stylist()
    {
        return $this->belongsTo(Stylist::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
