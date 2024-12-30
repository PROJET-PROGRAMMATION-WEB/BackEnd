<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'stylist_id',
        'rating',
        'comment',
        'is_verified_purchase',
        'is_approved'
    ];

    protected $casts = [
        'rating' => 'decimal:2'
    ];

    public function stylist()
    {
        return $this->belongsTo(Stylist::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
