<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['product_category_id', 'name', 'description', 'price', 'discount', 'stock', 'image', 'is_active'])]
class Product extends Model
{
    use HasFactory;

    protected $appends = ['discounted_price', 'has_discount'];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function getDiscountedPriceAttribute(): float
    {
        return round($this->price * $this->discount, 2);
    }

    public function getHasDiscountAttribute(): bool
    {
        return $this->discount < 1.00;
    }
}