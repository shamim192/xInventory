<?php

namespace App\Models;

use App\Models\Traits\HasDateRangeFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_holder_id', 
        'type', 
        'date', 
        'note', 
        'amount'
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
    public function loanHolder()
    {
        return $this->belongsTo(LoanHolder::class);
    }

    public function transactionFor()
    {
        return $this->loanHolder();
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'flagable');
    }
}
