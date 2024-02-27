<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_id','category_id', 'product_id', 'unit_id', 'unit_quantity', 'quantity', 'unit_price', 'amount', 'actual_quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function baseUnit()
    {
        return $this->belongsTo(BaseUnit::class);
    }   
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }   
}
