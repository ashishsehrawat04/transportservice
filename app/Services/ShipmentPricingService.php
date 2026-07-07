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

        if (!empty($item['length_cm']) && !empty($item['width_cm']) && !empty($item['height_cm'])) {
            $volumeCft = round(((float) $item['length_cm'] * (float) $item['width_cm'] * (float) $item['height_cm']) / 28316.8466, 2);
        }

        $item['volume_cft'] = $volumeCft;

        return $this->calculate($item, $route);
    }

    /**
     * Pricing is driven entirely by the city route: whichever of weight or
     * volume is larger for the item decides the charge basis, at the
     * route's per-kg / per-cft rate. The route's min_charge is a
     * once-per-shipment base fee and is intentionally NOT added here —
     * callers add it once when aggregating a shipment's items.
     */
    public function calculate(array $item, CityRoute $route): array
    {
        $quantity = max(1, (int) ($item['quantity'] ?? 1));
        $weightKg = (float) ($item['weight_kg'] ?? 0);
        $volumeCft = (float) ($item['volume_cft'] ?? 0);

        $actualWeightKg = round($weightKg * $quantity, 2);
        $totalVolumeCft = round($volumeCft * $quantity, 2);

        $weightRate = (float) $route->base_rate_per_weight;
        $volumeRate = (float) $route->base_rate_per_volume;

        $calculationType = $actualWeightKg > $totalVolumeCft ? 'weight' : 'volume';
        $weightCharge = $calculationType === 'weight' ? round($actualWeightKg * $weightRate, 2) : 0.0;
        $volumeCharge = $calculationType === 'volume' ? round($totalVolumeCft * $volumeRate, 2) : 0.0;

        $subtotal = round($weightCharge + $volumeCharge, 2);
        $taxAmount = round((float) ($item['tax_amount'] ?? 0), 2);
        $discountAmount = round((float) ($item['discount_amount'] ?? 0), 2);
        $totalPayment = max(0, round($subtotal + $taxAmount - $discountAmount, 2));

        $this->logCalculation([
            'calculation_type' => $calculationType,
            'volume_cft' => $totalVolumeCft,
            'distance_km' => $route->distance_km,
            'base_price' => 0,
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
            'distance_km' => $route->distance_km,
            'calculation_type' => $calculationType,
            'base_price' => 0,
            'weight_charge' => $weightCharge,
            'volume_charge' => $volumeCharge,
            'distance_charge' => 0,
            'multiplier_applied' => 1,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_payment' => $totalPayment,
            'charge_basis' => $calculationType,
            'charge_weight_kg' => $calculationType === 'weight' ? $actualWeightKg : $totalVolumeCft,
            'actual_weight_kg' => $actualWeightKg,
            'volumetric_weight_kg' => 0,
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
