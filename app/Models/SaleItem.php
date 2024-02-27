<?php

namespace App\Models;

use App\Models\Traits\HasCreatedAtDateRangeFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'category_id',
        'product_id',
        'unit_id',
        'unit_quantity',
        'quantity',
        'unit_price',
        'discount_percentage',
        'discount_amount',
        'flat_discount_percentage',
        'flat_discount_amount',
        'net_unit_price',
        'net_price',
        'amount',
        'actual_quantity',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
