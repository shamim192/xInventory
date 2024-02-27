<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseUnit extends Model
{
    protected $fillable = ['name','status'];
    
    public function units()
    {
        return $this->hasMany(Unit::class);
    }
}
