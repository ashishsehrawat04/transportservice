<?php

namespace Tests\Unit;

use App\Models\CityRoute;
use App\Models\TransportCartItem;
use App\Services\ShipmentPricingService;
use PHPUnit\Framework\TestCase;

class ShipmentPricingServiceTest extends TestCase
{
    public function test_it_charges_by_weight_when_weight_exceeds_volume(): void
    {
        $service = new ShipmentPricingService();
        $route = new CityRoute([
            'distance_km' => 20,
            'base_rate_per_weight' => 30,
            'base_rate_per_volume' => 10,
            'min_charge' => 300,
        ]);

        $result = $service->calculate([
            'quantity' => 2,
            'weight_kg' => 4,
            'volume_cft' => 3,
            'tax_amount' => 50,
            'discount_amount' => 25,
        ], $route);

        // actual weight = 4 * 2 = 8kg, volume = 3 * 2 = 6cft -> weight wins
        $this->assertSame('weight', $result['calculation_type']);
        $this->assertSame(240.0, $result['weight_charge']);
        $this->assertSame(0.0, $result['volume_charge']);
        $this->assertSame(240.0, $result['subtotal']);
        // route min_charge is NOT included here — it is added once per shipment by the caller
        $this->assertSame(265.0, $result['total_payment']);
    }

    public function test_it_charges_by_volume_when_volume_exceeds_weight(): void
    {
        $service = new ShipmentPricingService();
        $route = new CityRoute([
            'distance_km' => 20,
            'base_rate_per_weight' => 30,
            'base_rate_per_volume' => 10,
            'min_charge' => 300,
        ]);

        $result = $service->calculate([
            'quantity' => 1,
            'weight_kg' => 2,
            'volume_cft' => 5,
        ], $route);

        $this->assertSame('volume', $result['calculation_type']);
        $this->assertSame(0.0, $result['weight_charge']);
        $this->assertSame(50.0, $result['volume_charge']);
        $this->assertSame(50.0, $result['total_payment']);
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
        $route = new CityRoute([
            'distance_km' => 0,
            'base_rate_per_weight' => 0,
            'base_rate_per_volume' => 1,
            'min_charge' => 0,
        ]);

        $result = $service->calculateCartItem($cartItem, $route);

        $this->assertSame(35.31, $result['volume_cft']);
        $this->assertSame('volume', $result['calculation_type']);
        $this->assertSame(35.31, $result['total_payment']);
    }
}
