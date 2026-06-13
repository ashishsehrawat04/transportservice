<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportQuote extends Model
{
    protected $fillable = [
        'user_id',
        'transport_lead_id',
        'invoice_number',
        'tracking_number',
        'customer_name',
        'customer_email',
        'customer_mobile',
        'item_name',
        'item_type',
        'quantity',
        'length_cm',
        'width_cm',
        'height_cm',
        'weight_kg',
        'volume_cft',
        'from_city',
        'to_city',
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
        'admin_status',
        'user_status',
        'payment_status',
        'requested_pickup_date',
        'confirmed_pickup_date',
        'expected_delivery_date',
        'actual_delivery_date',
        'admin_description',
        'special_instructions',
        'quote_data',
    ];

    protected $casts = [
        'requested_pickup_date' => 'date',
        'confirmed_pickup_date' => 'date',
        'expected_delivery_date' => 'date',
        'actual_delivery_date' => 'date',
        'quote_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transportLead()
    {
        return $this->belongsTo(TransportLead::class);
    }

    public static function syncFromLead(TransportLead $lead, ?string $invoiceNumber = null): self
    {
        $lead->loadMissing(['user', 'cityRoute', 'latestPayment']);
        $invoiceNumber ??= $lead->latestPayment?->invoice_number;

        return self::updateOrCreate(
            ['transport_lead_id' => $lead->id],
            [
                'user_id' => $lead->user_id,
                'invoice_number' => $invoiceNumber,
                'tracking_number' => $lead->tracking_number,
                'customer_name' => optional($lead->user)->name,
                'customer_email' => optional($lead->user)->email,
                'customer_mobile' => optional($lead->user)->mobile,
                'item_name' => $lead->item_name,
                'item_type' => $lead->item_type,
                'quantity' => $lead->quantity,
                'length_cm' => $lead->length_cm,
                'width_cm' => $lead->width_cm,
                'height_cm' => $lead->height_cm,
                'weight_kg' => $lead->weight_kg,
                'volume_cft' => $lead->volume_cft,
                'from_city' => optional($lead->cityRoute)->from_city,
                'to_city' => optional($lead->cityRoute)->to_city,
                'distance_km' => $lead->distance_km,
                'base_price' => $lead->base_price,
                'weight_charge' => $lead->weight_charge,
                'volume_charge' => $lead->volume_charge,
                'distance_charge' => $lead->distance_charge,
                'multiplier_applied' => $lead->multiplier_applied,
                'subtotal' => $lead->subtotal,
                'tax_amount' => $lead->tax_amount,
                'discount_amount' => $lead->discount_amount,
                'total_payment' => $lead->total_payment,
                'admin_status' => $lead->admin_status,
                'user_status' => $lead->user_status,
                'payment_status' => $lead->payment_status,
                'requested_pickup_date' => $lead->requested_pickup_date,
                'confirmed_pickup_date' => $lead->confirmed_pickup_date,
                'expected_delivery_date' => $lead->expected_delivery_date,
                'actual_delivery_date' => $lead->actual_delivery_date,
                'admin_description' => $lead->admin_description,
                'special_instructions' => $lead->special_instructions,
                'quote_data' => $lead->toArray(),
            ]
        );
    }
}
