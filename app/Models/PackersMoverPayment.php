<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackersMoverPayment extends Model
{
    protected $fillable = [
        'invoice_number',
        'user_id',
        'packers_mover_lead_id',
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

    public function packersMoverLead()
    {
        return $this->belongsTo(PackersMoverLead::class);
    }
}
