<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseQuote extends Model
{
    protected $fillable = [
        'user_id',
        'warehouse_lead_id',
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
        'warehouse_name',
        'warehouse_city',
        'warehouse_address',
        'storage_days',
        'calculation_type',
        'base_price',
        'weight_charge',
        'volume_charge',
        'multiplier_applied',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_payment',
        'admin_status',
        'user_status',
        'payment_status',
        'requested_pickup_date',
        'admin_description',
        'special_instructions',
        'quote_data',
    ];

    protected $casts = [
        'requested_pickup_date' => 'date',
        'quote_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warehouseLead()
    {
        return $this->belongsTo(WarehouseLead::class);
    }

    public static function syncFromLead(WarehouseLead $lead, ?string $invoiceNumber = null): self
    {
        $lead->loadMissing(['user', 'warehouse', 'latestPayment']);
        $invoiceNumber ??= $lead->latestPayment?->invoice_number;

        // quote_data below is refreshed from the lead's current fields on
        // every sync, but the per-item storage breakdown and pickup address
        // only exist at checkout time — preserve them instead of letting
        // them get wiped out by later re-syncs (status changes, payments,
        // admin edits, etc).
        $existingData = self::where('warehouse_lead_id', $lead->id)->value('quote_data') ?? [];
        $quoteData = $lead->toArray();

        if (!empty($existingData['warehouse_items'])) {
            $quoteData['warehouse_items'] = $existingData['warehouse_items'];
        }

        if (!empty($existingData['warehouse_address'])) {
            $quoteData['warehouse_address'] = $existingData['warehouse_address'];
        }

        return self::updateOrCreate(
            ['warehouse_lead_id' => $lead->id],
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
                'warehouse_name' => optional($lead->warehouse)->name,
                'warehouse_city' => optional($lead->warehouse)->city,
                'warehouse_address' => optional($lead->warehouse)->address,
                'storage_days' => $lead->storage_days,
                'calculation_type' => $lead->calculation_type,
                'base_price' => $lead->base_price,
                'weight_charge' => $lead->weight_charge,
                'volume_charge' => $lead->volume_charge,
                'multiplier_applied' => $lead->multiplier_applied,
                'subtotal' => $lead->subtotal,
                'tax_amount' => $lead->tax_amount,
                'discount_amount' => $lead->discount_amount,
                'total_payment' => $lead->total_payment,
                'admin_status' => $lead->admin_status,
                'user_status' => $lead->user_status,
                'payment_status' => $lead->payment_status,
                'requested_pickup_date' => $lead->requested_pickup_date,
                'admin_description' => $lead->admin_description,
                'special_instructions' => $lead->special_instructions,
                'quote_data' => $quoteData,
            ]
        );
    }
}
