<?php

namespace Tests\Unit;

use App\Models\CityRoute;
use App\Models\TransportCartItem;
use App\Services\ShipmentPricingService;
use PHPUnit\Framework\TestCase;

class ShipmentPricingServiceTest extends TestCase
{
    public function test_it_charges_by_weight_when_weight_is_the_bigger_figure(): void
    {
        $service = new ShipmentPricingService();
        $route = new CityRoute([
            'rate_per_weight' => 30,
            'min_charge' => 100,
        ]);

        // actual weight = 4 * 2 = 8kg, no dimensions so volumetric weight = 0.
        // charge = 8 * 30 = 240, above min_charge so it stays 240.
        $result = $service->calculate([
            'quantity' => 2,
            'weight_kg' => 4,
            'tax_amount' => 50,
            'discount_amount' => 25,
        ], $route);

        $this->assertSame('weight', $result['charge_basis']);
        $this->assertSame(8.0, $result['charge_weight_kg']);
        $this->assertSame(240.0, $result['weight_charge']);
        $this->assertSame(0.0, $result['volume_charge']);
        $this->assertSame(240.0, $result['subtotal']);
        $this->assertSame(265.0, $result['total_payment']);
    }

    public function test_it_charges_by_volumetric_weight_when_volume_is_the_bigger_figure(): void
    {
        $service = new ShipmentPricingService();
        $route = new CityRoute([
            'rate_per_weight' => 10,
            'min_charge' => 0,
        ]);

        // 90 x 50 x 50 cm = 225000 cm3 / 4500 = 50kg volumetric weight,
        // vs a tiny 2kg actual weight — volume must win.
        $result = $service->calculateFromDimensions([
            'quantity' => 1,
            'length_cm' => 90,
            'width_cm' => 50,
            'height_cm' => 50,
            'weight_kg' => 2,
        ], $route);

        $this->assertSame('volume', $result['charge_basis']);
        $this->assertSame(50.0, $result['volumetric_weight_kg']);
        $this->assertSame(50.0, $result['charge_weight_kg']);
        $this->assertSame(500.0, $result['volume_charge']);
        $this->assertSame(0.0, $result['weight_charge']);
        $this->assertSame(500.0, $result['total_payment']);
    }

    public function test_min_charge_floors_the_price_when_item_total_is_below_it(): void
    {
        $service = new ShipmentPricingService();
        $route = new CityRoute([
            'rate_per_weight' => 10,
            'min_charge' => 500,
        ]);

        // chargeable weight = 3kg, raw charge = 30, below the 500 min_charge floor.
        $result = $service->calculate([
            'quantity' => 1,
            'weight_kg' => 3,
        ], $route);

        $this->assertSame(500.0, $result['subtotal']);
        $this->assertSame(500.0, $result['total_payment']);
    }

    public function test_item_total_above_min_charge_is_used_as_is(): void
    {
        $service = new ShipmentPricingService();
        $route = new CityRoute([
            'rate_per_weight' => 10,
            'min_charge' => 50,
        ]);

        // chargeable weight = 20kg, raw charge = 200, above the 50 min_charge floor.
        $result = $service->calculate([
            'quantity' => 1,
            'weight_kg' => 20,
        ], $route);

        $this->assertSame(200.0, $result['subtotal']);
        $this->assertSame(200.0, $result['total_payment']);
    }

    public function test_it_calculates_volumetric_weight_from_centimeters_in_kg_not_cft(): void
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
            'rate_per_weight' => 0,
            'min_charge' => 0,
        ]);

        $result = $service->calculateCartItem($cartItem, $route);

        // 100 * 100 * 100 = 1,000,000 cm3 / 4500 = 222.22kg volumetric weight.
        $this->assertSame(222.22, $result['volumetric_weight_kg']);
        $this->assertSame('volume', $result['charge_basis']);
        $this->assertSame(35.31, $result['volume_cft']);
    }
}
