<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentRefund extends Model
{
    protected $fillable = [
        'user_id',
        'transport_lead_id',
        'shipment_payment_id',
        'amount',
        'status',
        'reason',
        'admin_note',
    ];
}
