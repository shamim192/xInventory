<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = ['name', 'quantity', 'base_unit_id'];
    
    public function baseUnit()
    {
        return $this->belongsTo(BaseUnit::class);
    }
}
