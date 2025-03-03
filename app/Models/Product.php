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
        'image',
        'rate',
        'count',
        'description',
        // 'category_id', // Thêm category_id để liên kết với bảng categories
    ];
   
    // public function category()
    // {
    //     return $this->belongsTo(Category::class);
    // }

    // public function orders()
    // {
    //     return $this->hasMany(OrderDetail::class);
    // }
}
