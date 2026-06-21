<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportLead extends Model
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
        'city_route_id',
        'distance_km',
        'calculation_type',
        'base_price',
        'weight_charge',
        'volume_charge',
        'distance_charge',
        'multiplier_applied',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_payment',
        'requested_pickup_date',
        'confirmed_pickup_date',
        'expected_delivery_date',
        'actual_delivery_date',
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
        'confirmed_pickup_date' => 'date',
        'expected_delivery_date' => 'date',
        'actual_delivery_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cityRoute()
    {
        return $this->belongsTo(CityRoute::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function payments()
    {
        return $this->hasMany(ShipmentPayment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(ShipmentPayment::class)->latestOfMany();
    }

    public function refunds()
    {
        return $this->hasMany(ShipmentRefund::class);
    }
}
