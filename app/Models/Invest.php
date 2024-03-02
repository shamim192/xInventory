<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invest extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_id', 'investor_id', 'date', 'note', 'amount',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }
}
