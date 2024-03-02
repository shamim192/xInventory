<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomeItem extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'income_id',
        'bank_id',
        'income_category_id',
        'amount',
    ];

    public function category()
    {
        return $this->belongsTo(IncomeCategory::class, 'income_category_id', 'id');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
