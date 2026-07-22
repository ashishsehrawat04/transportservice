<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehousePayment extends Model
{
    protected $fillable = [
        'invoice_number',
        'user_id',
        'warehouse_lead_id',
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

    public function warehouseLead()
    {
        return $this->belongsTo(WarehouseLead::class);
    }
}
