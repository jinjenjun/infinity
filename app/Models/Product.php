<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_category_id',
        'admin_id',
        'name',
        'description',
        'price',
        'discount',
        'stock',
        'image',
        'is_active',
    ];

    protected $appends = ['discounted_price', 'has_discount'];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function getDiscountedPriceAttribute(): float
    {
        return round($this->price * $this->discount, 2);
    }

    public function getHasDiscountAttribute(): bool
    {
        return $this->discount < 1.00;
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }
}