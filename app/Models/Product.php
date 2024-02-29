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
    public static function getStock($id)
    {
        $data = Product::select(DB::raw("((IFNULL(A.inQty, 0) + IFNULL(D.inQty, 0)) - (IFNULL(B.outQty, 0) + IFNULL(C.outQty, 0))) AS stockQty"))
            ->join(DB::raw("(SELECT product_id, SUM(actual_quantity) AS inQty FROM stock_items GROUP BY product_id) AS A"), function ($q) {
                $q->on('A.product_id', '=', 'products.id');
            })
            ->leftJoin(DB::raw("(SELECT product_id, SUM(actual_quantity) AS outQty FROM stock_return_items GROUP BY product_id) AS B"), function ($q) {
                $q->on('B.product_id', '=', 'products.id');
            })
            ->leftJoin(DB::raw("(SELECT product_id, SUM(actual_quantity) AS outQty FROM sale_items GROUP BY product_id) AS C"), function ($q) {
                $q->on('C.product_id', '=', 'products.id');
            })
            ->leftJoin(DB::raw("(SELECT product_id, SUM(actual_quantity) AS inQty FROM sale_return_items GROUP BY product_id) AS D"), function ($q) {
                $q->on('D.product_id', '=', 'products.id');
            })
            ->find($id);
        return $data->stockQty;
    }
}
