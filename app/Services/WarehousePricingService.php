<?php

namespace App\Services;

use App\Models\Warehouse;
use App\Models\WarehouseCartItem;

class WarehousePricingService
{
    /**
     * cm³ per "volumetric kg" for surface/road cargo. Volumetric weight
     * (kg) = (L * W * H in cm) / VOLUMETRIC_DIVISOR.
     */
    private const VOLUMETRIC_DIVISOR = 4500;

    public function calculateCartItem(WarehouseCartItem $item, Warehouse $warehouse): array
    {
        return $this->calculateFromDimensions([
            'quantity' => $item->quantity,
            'length_cm' => $item->length_cm,
            'width_cm' => $item->width_cm,
            'height_cm' => $item->height_cm,
            'weight_kg' => $item->weight_kg,
            'storage_days' => $item->storage_days,
        ], $warehouse);
    }

    public function calculateFromDimensions(array $item, Warehouse $warehouse): array
    {
        $volumeCft = 0;
        $volumetricWeightKg = 0;

        if (!empty($item['length_cm']) && !empty($item['width_cm']) && !empty($item['height_cm'])) {
            $volumeCm3 = (float) $item['length_cm'] * (float) $item['width_cm'] * (float) $item['height_cm'];
            $volumeCft = round($volumeCm3 / 28316.8466, 2);
            $volumetricWeightKg = round($volumeCm3 / self::VOLUMETRIC_DIVISOR, 2);
        }

        $item['volume_cft'] = $volumeCft;
        $item['volumetric_weight_kg'] = $volumetricWeightKg;

        return $this->calculate($item, $warehouse);
    }

    /**
     * Chargeable weight is the greater of actual weight and volumetric
     * weight (both in kg) — same "whichever is higher" rule as shipment
     * billing. That chargeable weight is billed at the warehouse's flat
     * per-day-per-kg rate, multiplied by the number of storage days. This
     * returns the raw item charge with no min_charge floor applied — the
     * floor is a request-level rule, not a per-item one, so callers must
     * apply it once across a request's combined total via
     * floorShipmentTotal().
     */
    public function calculate(array $item, Warehouse $warehouse): array
    {
        $quantity = max(1, (int) ($item['quantity'] ?? 1));
        $weightKg = (float) ($item['weight_kg'] ?? 0);
        $volumeCft = (float) ($item['volume_cft'] ?? 0);
        $volumetricWeightKgPerUnit = (float) ($item['volumetric_weight_kg'] ?? 0);
        $storageDays = max(1, (int) ($item['storage_days'] ?? 1));

        $actualWeightKg = round($weightKg * $quantity, 2);
        $totalVolumeCft = round($volumeCft * $quantity, 2);
        $volumetricWeightKg = round($volumetricWeightKgPerUnit * $quantity, 2);

        $chargeBasis = $volumetricWeightKg > $actualWeightKg ? 'volume' : 'weight';
        $chargeableWeightKg = max($actualWeightKg, $volumetricWeightKg);

        $rate = (float) $warehouse->price_per_day_per_kg;
        $rawCharge = round($chargeableWeightKg * $rate * $storageDays, 2);

        $minCharge = round((float) $warehouse->min_charge, 2);
        $itemCharge = $rawCharge;

        $weightCharge = $chargeBasis === 'weight' ? $itemCharge : 0.0;
        $volumeCharge = $chargeBasis === 'volume' ? $itemCharge : 0.0;

        $subtotal = $itemCharge;
        $taxAmount = round((float) ($item['tax_amount'] ?? 0), 2);
        $discountAmount = round((float) ($item['discount_amount'] ?? 0), 2);
        $totalPayment = max(0.0, round($subtotal + $taxAmount - $discountAmount, 2));

        return [
            'volume_cft' => $totalVolumeCft,
            'calculation_type' => $chargeBasis,
            'base_price' => $minCharge,
            'weight_charge' => $weightCharge,
            'volume_charge' => $volumeCharge,
            'multiplier_applied' => 1,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_payment' => $totalPayment,
            'charge_basis' => $chargeBasis,
            'charge_weight_kg' => $chargeableWeightKg,
            'actual_weight_kg' => $actualWeightKg,
            'volumetric_weight_kg' => $volumetricWeightKg,
            'billing_volume_cft' => $totalVolumeCft,
            'storage_days' => $storageDays,
        ];
    }

    /**
     * Applies the warehouse's min_charge as a single floor across a whole
     * request's combined item total. Call this once on the sum of every
     * item's raw charge in the request/group — never per individual item.
     */
    public function floorShipmentTotal(float $itemsTotal, Warehouse $warehouse): float
    {
        $minCharge = round((float) $warehouse->min_charge, 2);

        return max(round($itemsTotal, 2), $minCharge);
    }
}
