<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanHolder extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'mobile', 'address', 'status',
    ];
}
