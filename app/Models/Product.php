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
}
