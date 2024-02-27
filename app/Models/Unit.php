<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = ['name', 'quantity', 'base_unit_id','status'];
    
    public function baseUnit()
    {
        return $this->belongsTo(BaseUnit::class, 'base_unit_id');
    }

    public function units()
    {
       $data = Unit::where('base_unit_id', $this->base_unit_id)->get(); 
        return $data;
    }
}
