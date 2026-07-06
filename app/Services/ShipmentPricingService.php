<?php

namespace App\Services;

use App\Models\CityRoute;
use App\Models\TransportCartItem;
use App\Models\TransportServicePrice;
use App\Models\ShipmentPriceLog;
use Illuminate\Support\Facades\Auth;
use RuntimeException;
use Throwable;

class ShipmentPricingService
{
    public function calculateCartItem(TransportCartItem $item, TransportServicePrice $price, CityRoute $route): array
    {
        return $this->calculateFromDimensions([
            'quantity' => $item->quantity,
            'length_cm' => $item->length_cm,
            'width_cm' => $item->width_cm,
            'height_cm' => $item->height_cm,
            'weight_kg' => $item->weight_kg,
        ], $price, $route);
    }

    public function calculateFromDimensions(array $item, TransportServicePrice $price, CityRoute $route): array
    {
        $volumeCft = 0;

        if (!empty($item['length_cm']) && !empty($item['width_cm']) && !empty($item['height_cm'])) {
            $volumeCft = round(((float) $item['length_cm'] * (float) $item['width_cm'] * (float) $item['height_cm']) / 28316.8466, 2);
        }

        $item['volume_cft'] = $volumeCft;

        return $this->calculate($item, $price, $route);
    }

    public function calculate(array $item, TransportServicePrice $price, CityRoute $route): array
    {
        $quantity = max(1, (int) ($item['quantity'] ?? 1));
        $weightKg = (float) ($item['weight_kg'] ?? 0);
        $volumeCft = (float) ($item['volume_cft'] ?? 0);
        $totalVolume = $volumeCft * $quantity;
        $routeMinCharge = round((float) $route->min_charge, 2);
        $basePricePerUnit = $routeMinCharge > 0 ? $routeMinCharge : round((float) $price->min_charge, 2);
        $basePrice = $basePricePerUnit * $quantity;
        $multiplier = (float) ($price->multiplier ?: 1);
        $routeWeightRate = (float) $route->base_rate_per_km;
        $routeVolumeRate = (float) $route->base_rate_per_volume;
        $routeWeightRate = $routeWeightRate > 0 ? $routeWeightRate : (float) $price->weight_rate_per_kg;
        $routeVolumeRate = $routeVolumeRate > 0 ? $routeVolumeRate : (float) $price->volume_rate_per_cft;

        $actualWeightKg = round($weightKg * $quantity, 2);
        $volumetricWeightKg = round($volumeCft * 5.66336, 2);
        $weightCharge = round($actualWeightKg * $routeWeightRate, 2);
        $calculatedVolumeCharge = round($totalVolume * $routeVolumeRate, 2);
        $calculationType = $volumetricWeightKg > $actualWeightKg ? 'volumetric' : 'weight';
        $volumeCharge = $calculationType === 'volumetric' ? $calculatedVolumeCharge : 0.0;
        $weightCharge = $calculationType === 'weight' ? $weightCharge : 0.0;
        $distanceCharge = 0.0;
        $selectedCharge = max($basePrice, max($weightCharge, $volumeCharge));
        $subtotal = round($selectedCharge * $multiplier, 2);
        $taxAmount = round((float) ($item['tax_amount'] ?? 0), 2);
        $discountAmount = round((float) ($item['discount_amount'] ?? 0), 2);
        $totalPayment = max(0, round($subtotal + $taxAmount - $discountAmount, 2));

        $this->logCalculation([
            'calculation_type' => $calculationType,
            'volume_cft' => $volumeCft,
            'distance_km' => $route->distance_km,
            'base_price' => $basePrice,
            'weight_charge' => $weightCharge,
            'volume_charge' => $volumeCharge,
            'distance_charge' => $distanceCharge,
            'multiplier_applied' => $multiplier,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_payment' => $totalPayment,
        ]);

        return [
            'volume_cft' => $volumeCft,
            'distance_km' => $route->distance_km,
            'calculation_type' => $calculationType,
            'base_price' => $basePrice,
            'weight_charge' => $weightCharge,
            'volume_charge' => $volumeCharge,
            'distance_charge' => $distanceCharge,
            'multiplier_applied' => $multiplier,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_payment' => $totalPayment,
            'charge_basis' => $calculationType,
            'charge_weight_kg' => $calculationType === 'weight' ? $actualWeightKg : $volumetricWeightKg,
            'actual_weight_kg' => $actualWeightKg,
            'volumetric_weight_kg' => $volumetricWeightKg,
            'billing_volume_cft' => $totalVolume,
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
