<?php

namespace App\Services;

use App\Models\CityRoute;
use App\Models\TransportCartItem;
use App\Models\TransportServicePrice;

class ShipmentPricingService
{
    public function calculateCartItem(TransportCartItem $item, TransportServicePrice $price, CityRoute $route): array
    {
        $quantity = (int) $item->quantity;
        $volumeCft = 0;

        if ($item->length_cm && $item->width_cm && $item->height_cm) {
            $volumeCft = round(((float) $item->length_cm * (float) $item->width_cm * (float) $item->height_cm) / 28316.8466, 2);
        }

        return $this->calculate([
            'quantity' => $quantity,
            'weight_kg' => $item->weight_kg,
            'volume_cft' => $volumeCft,
        ], $price, $route);
    }

    public function calculate(array $item, TransportServicePrice $price, CityRoute $route): array
    {
        $quantity = (int) ($item['quantity'] ?? 1);
        $volumeCft = (float) ($item['volume_cft'] ?? 0);
        $totalWeight = (float) ($item['weight_kg'] ?? 0) * $quantity;
        $totalVolume = $volumeCft * $quantity;
        $basePrice = (float) $price->base_price;
        $weightCharge = round($totalWeight * (float) $price->weight_rate_per_kg, 2);
        $volumeCharge = round($totalVolume * (float) $price->volume_rate_per_cft, 2);
        $distanceCharge = round((float) $route->distance_km * (float) $price->distance_rate_per_km, 2);
        $subtotal = round(($basePrice + $weightCharge + $volumeCharge + $distanceCharge) * (float) $price->multiplier, 2);

        if ($subtotal < (float) $price->min_charge) {
            $subtotal = (float) $price->min_charge;
        }

        if ($price->max_charge && $subtotal > (float) $price->max_charge) {
            $subtotal = (float) $price->max_charge;
        }

        return [
            'volume_cft' => $volumeCft,
            'distance_km' => $route->distance_km,
            'base_price' => $basePrice,
            'weight_charge' => $weightCharge,
            'volume_charge' => $volumeCharge,
            'distance_charge' => $distanceCharge,
            'multiplier_applied' => $price->multiplier,
            'subtotal' => $subtotal,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_payment' => $subtotal,
        ];
    }
}
