<?php

namespace App\Services;

use App\Models\CityRoute;
use App\Models\TransportCartItem;
use App\Models\ShipmentPriceLog;
use Illuminate\Support\Facades\Auth;
use RuntimeException;
use Throwable;

class ShipmentPricingService
{
    /**
     * cm³ per "volumetric kg" for surface/road cargo. Volumetric weight
     * (kg) = (L * W * H in cm) / VOLUMETRIC_DIVISOR.
     */
    private const VOLUMETRIC_DIVISOR = 4500;

    public function calculateCartItem(TransportCartItem $item, CityRoute $route): array
    {
        return $this->calculateFromDimensions([
            'quantity' => $item->quantity,
            'length_cm' => $item->length_cm,
            'width_cm' => $item->width_cm,
            'height_cm' => $item->height_cm,
            'weight_kg' => $item->weight_kg,
        ], $route);
    }

    public function calculateFromDimensions(array $item, CityRoute $route): array
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

        return $this->calculate($item, $route);
    }

    /**
     * Chargeable weight is the greater of actual weight and volumetric
     * weight (both in kg) — standard courier "whichever is higher"
     * billing. That chargeable weight is billed at the route's flat
     * per-kg rate. If the resulting item price is below the route's
     * min_charge, the min_charge is used instead (a per-item floor, not
     * an additive fee).
     */
    public function calculate(array $item, CityRoute $route): array
    {
        $quantity = max(1, (int) ($item['quantity'] ?? 1));
        $weightKg = (float) ($item['weight_kg'] ?? 0);
        $volumeCft = (float) ($item['volume_cft'] ?? 0);
        $volumetricWeightKgPerUnit = (float) ($item['volumetric_weight_kg'] ?? 0);

        $actualWeightKg = round($weightKg * $quantity, 2);
        $totalVolumeCft = round($volumeCft * $quantity, 2);
        $volumetricWeightKg = round($volumetricWeightKgPerUnit * $quantity, 2);

        $chargeBasis = $volumetricWeightKg > $actualWeightKg ? 'volume' : 'weight';
        $chargeableWeightKg = max($actualWeightKg, $volumetricWeightKg);

        $rate = (float) $route->rate_per_weight;
        $rawCharge = round($chargeableWeightKg * $rate, 2);

        $minCharge = round((float) $route->min_charge, 2);
        $itemCharge = $rawCharge < $minCharge ? $minCharge : $rawCharge;

        $weightCharge = $chargeBasis === 'weight' ? $itemCharge : 0.0;
        $volumeCharge = $chargeBasis === 'volume' ? $itemCharge : 0.0;

        $subtotal = $itemCharge;
        $taxAmount = round((float) ($item['tax_amount'] ?? 0), 2);
        $discountAmount = round((float) ($item['discount_amount'] ?? 0), 2);
        $totalPayment = max(0.0, round($subtotal + $taxAmount - $discountAmount, 2));

        $this->logCalculation([
            'calculation_type' => $chargeBasis,
            'volume_cft' => $totalVolumeCft,
            'base_price' => $minCharge,
            'weight_charge' => $weightCharge,
            'volume_charge' => $volumeCharge,
            'distance_charge' => 0,
            'multiplier_applied' => 1,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_payment' => $totalPayment,
        ]);

        return [
            'volume_cft' => $totalVolumeCft,
            'calculation_type' => $chargeBasis,
            'base_price' => $minCharge,
            'weight_charge' => $weightCharge,
            'volume_charge' => $volumeCharge,
            'distance_charge' => 0,
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
        ];
    }

    private function logCalculation(array $data): void
    {
        $user = $this->authUser();

        try {
            ShipmentPriceLog::create(array_merge($data, [
                'user_id' => $user?->id,
                'user_name' => $user?->name,
                'user_email' => $user?->email,
            ]));
        } catch (Throwable) {
            // Pricing should stay usable in pure calculation contexts without an app/database.
        }
    }

    private function authUser(): ?object
    {
        try {
            return Auth::user();
        } catch (RuntimeException) {
            return null;
        }
    }
}
