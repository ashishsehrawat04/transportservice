<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackersMoverLead extends Model
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
        'packers_mover_id',
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
        'distance_km',
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

    public function packersMover()
    {
        return $this->belongsTo(PackersMover::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function payments()
    {
        return $this->hasMany(PackersMoverPayment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(PackersMoverPayment::class)->latestOfMany();
    }
}
