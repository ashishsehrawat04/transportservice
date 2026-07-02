<?php

namespace Tests\Unit;

use App\Models\CityRoute;
use App\Models\TransportCartItem;
use App\Models\TransportServicePrice;
use App\Services\ShipmentPricingService;
use PHPUnit\Framework\TestCase;

class ShipmentPricingServiceTest extends TestCase
{
    public function test_it_uses_route_min_charge_and_prefers_higher_weight_or_volume_charge(): void
    {
        $service = new ShipmentPricingService();
        $price = new TransportServicePrice([
            'multiplier' => 1,
            'min_charge' => 200,
        ]);
        $route = new CityRoute([
            'distance_km' => 20,
            'base_rate_per_km' => 30,
            'base_rate_per_volume' => 10,
            'min_charge' => 300,
        ]);

        $result = $service->calculate([
            'quantity' => 2,
            'weight_kg' => 4,
            'volume_cft' => 3,
            'tax_amount' => 50,
            'discount_amount' => 25,
        ], $price, $route);

        $this->assertSame('weight', $result['calculation_type']);
        $this->assertSame(240.0, $result['weight_charge']);
        $this->assertSame(0.0, $result['volume_charge']);
        $this->assertSame(300.0, $result['subtotal']);
        $this->assertSame(325.0, $result['total_payment']);
    }

    public function test_it_calculates_cart_item_volume_from_centimeters(): void
    {
        $service = new ShipmentPricingService();
        $cartItem = new TransportCartItem([
            'quantity' => 1,
            'length_cm' => 100,
            'width_cm' => 100,
            'height_cm' => 100,
            'weight_kg' => 10,
        ]);
        $price = new TransportServicePrice([
            'multiplier' => 1,
            'min_charge' => 0,
        ]);
        $route = new CityRoute([
            'distance_km' => 0,
            'base_rate_per_km' => 0,
            'base_rate_per_volume' => 1,
            'min_charge' => 0,
        ]);

        $result = $service->calculateCartItem($cartItem, $price, $route);

        $this->assertSame(35.31, $result['volume_cft']);
        $this->assertSame(35.31, $result['total_payment']);
    }
}
