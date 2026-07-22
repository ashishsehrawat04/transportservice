<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseCartItem extends Model
{
    protected $fillable = [
        'user_id',
        'guest_id',
        'warehouse_id',
        'item_name',
        'item_type',
        'quantity',
        'length_cm',
        'width_cm',
        'height_cm',
        'weight_kg',
        'pickup_date',
        'storage_days',
        'estimated_total',
        'charge_basis',
        'charge_weight_kg',
        'volumetric_weight_kg',
        'warehouse_lead_id',
        'booking_status',
    ];

    protected $casts = [
        'pickup_date' => 'date',
        'charge_weight_kg' => 'decimal:2',
        'volumetric_weight_kg' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function warehouseAddress()
    {
        return $this->hasOne(WarehouseAddress::class, 'item_id');
    }

    public function warehouseLead()
    {
        return $this->belongsTo(WarehouseLead::class);
    }
}
