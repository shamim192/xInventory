<?php

namespace App\Models;

use App\Models\Traits\HasDateRangeFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'type', 'date', 'note', 'amount', 'sale_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'flagable');
    }

}
