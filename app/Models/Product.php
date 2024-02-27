<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'model',
        'bn_name',
        'category_id',        
        'base_unit_id',
        'purchase_price',        
        'mrp',
        'discount_percentage',
        'status',
    ];

    public function baseUnit()
    {
        return $this->belongsTo(BaseUnit::class, 'base_unit_id');
    }

    public function units()
    {
       $data = Unit::where('base_unit_id', $this->base_unit_id)->get(); 
        return $data;
    }

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }
}
