<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseLead extends Model
{
    protected $fillable = [
        'user_id',
        'item_name',
        'item_type',
        'quantity',
        'length_cm',
        'width_cm',
        'height_cm',
        'weight_kg',
        'volume_cft',
        'warehouse_id',
        'calculation_type',
        'base_price',
        'weight_charge',
        'volume_charge',
        'multiplier_applied',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_payment',
        'requested_pickup_date',
        'storage_days',
        'admin_status',
        'admin_description',
        'assigned_to',
        'user_status',
        'payment_status',
        'payment_method',
        'transaction_id',
        'special_instructions',
        'tracking_number',
    ];

    protected $casts = [
        'requested_pickup_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function payments()
    {
        return $this->hasMany(WarehousePayment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(WarehousePayment::class)->latestOfMany();
    }
}
