<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Product;
use App\Models\Stylist;
use App\Models\Payment;
use App\Models\Review;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'stylist_id',
        'status',
        'total_price',
        'measurements',
        'customization_details',
        'expected_delivery_date',
        'notes'
    ];

    protected $casts = [
        'measurements' => 'array',
        'customization_details' => 'array',
        'total_price' => 'decimal:2',
        'expected_delivery_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stylist()
    {
        return $this->belongsTo(Stylist::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
