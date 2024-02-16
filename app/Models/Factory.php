<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factory extends Model
{
    protected $fillable = [
        'name',
        'address',
        'google_map',
        'email',
        'mobile_no',
        'status',
    ];
}
