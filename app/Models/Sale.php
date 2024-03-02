<?php

namespace App\Models;

use App\Models\Traits\HasDateRangeFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'invoice_no', 'date', 'total_quantity', 'subtotal_amount', 'flat_discount_percent', 'flat_discount_amount', 'vat_percent', 'vat_amount', 'total_amount',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class, 'sale_id', 'id');
    }
}
