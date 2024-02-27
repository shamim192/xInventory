<?php

namespace App\Models;

use App\Models\Traits\HasDateRangeFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupplierPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id', 'type', 'date', 'note', 'amount', 'stock_id',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function transactionFor()
    {
        return $this->supplier();
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'flagable');
    }
}
