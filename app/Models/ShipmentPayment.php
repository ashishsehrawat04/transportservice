<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentPayment extends Model
{
    protected $fillable = [
        'user_id',
        'transport_lead_id',
        'amount',
        'method',
        'status',
        'transaction_id',
        'notes',
    ];
}
