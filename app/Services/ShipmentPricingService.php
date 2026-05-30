<?php

namespace App\Services;

use App\Models\CityRoute;
use App\Models\TransportCartItem;
use App\Models\TransportServicePrice;

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
        $quantity = (int) ($item['quantity'] ?? 1);
        $volumeCft = (float) ($item['volume_cft'] ?? 0);
        $totalWeight = (float) ($item['weight_kg'] ?? 0) * $quantity;
        $totalVolume = $volumeCft * $quantity;
        $basePrice = (float) $price->base_price;
        $weightCharge = round($totalWeight * (float) $price->weight_rate_per_kg, 2);
        $volumeCharge = round($totalVolume * (float) $price->volume_rate_per_cft, 2);
        $distanceRate = (float) $route->base_rate_per_km > 0
            ? (float) $route->base_rate_per_km
            : (float) $price->distance_rate_per_km;
        $distanceCharge = round((float) $route->distance_km * $distanceRate, 2);
        $subtotal = round(($basePrice + $weightCharge + $volumeCharge + $distanceCharge) * (float) $price->multiplier, 2);
        $minimumCharge = max((float) $price->min_charge, (float) $route->min_charge);

        if ($subtotal < $minimumCharge) {
            $subtotal = $minimumCharge;
        }

        if ($price->max_charge && $subtotal > (float) $price->max_charge) {
            $subtotal = (float) $price->max_charge;
        }

        $taxAmount = (float) ($item['tax_amount'] ?? 0);
        $discountAmount = (float) ($item['discount_amount'] ?? 0);

        return [
            'volume_cft' => $volumeCft,
            'distance_km' => $route->distance_km,
            'base_price' => $basePrice,
            'weight_charge' => $weightCharge,
            'volume_charge' => $volumeCharge,
            'distance_charge' => $distanceCharge,
            'multiplier_applied' => $price->multiplier,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_payment' => max(0, round($subtotal + $taxAmount - $discountAmount, 2)),
        ];
    }
}
