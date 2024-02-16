<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
     protected $fillable = [
    'name',
    'type',
    'address',
    'google_map',
    'email',
    'mobile_no',
    'status',
];
}
