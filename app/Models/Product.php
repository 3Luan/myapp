<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'rate',
        'count',
        'description',
    ];

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'discount_product');
    }

    // public function getFinalPriceAttribute()
    // {
    //     $discount = $this->discounts()
    //         ->where('start_date', '<=', now())
    //         ->where('end_date', '>=', now())
    //         ->orderByDesc('discount_value')
    //         ->first();

    //     if ($discount) {
    //         if ($discount->discount_type === 'fixed') {
    //             return max(0, $this->price - $discount->discount_value);
    //         } elseif ($discount->discount_type === 'percent') {
    //             return max(0, $this->price * (1 - $discount->discount_value / 100));
    //         }
    //     }

    //     return $this->price;
    // }
}
