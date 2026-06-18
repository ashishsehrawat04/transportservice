<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentAddress extends Model
{
    protected $fillable = [
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'status',
        'country',
        'pincode',
    ];
}
