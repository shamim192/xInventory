<?php

namespace App\Models;

use App\Models\Traits\HasDateRangeFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'date',
        'note',
        'total_amount'
    ];

    public function items()
    {
        return $this->hasMany(ExpenseItem::class);
    }
}
