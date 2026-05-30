<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentPayment extends Model
{
    protected $fillable = [
        'invoice_number',
        'user_id',
        'transport_lead_id',
        'amount',
        'method',
        'status',
        'transaction_id',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transportLead()
    {
        return $this->belongsTo(TransportLead::class);
    }
}
