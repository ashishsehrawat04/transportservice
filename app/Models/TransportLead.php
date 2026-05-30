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
        'from_city_id',
        'to_city_id',
        'distance_km',
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

    public function fromCity()
    {
        return $this->belongsTo(City::class, 'from_city_id');
    }

    public function toCity()
    {
        return $this->belongsTo(City::class, 'to_city_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function payments()
    {
        return $this->hasMany(ShipmentPayment::class);
    }

    public function refunds()
    {
        return $this->hasMany(ShipmentRefund::class);
    }
}
