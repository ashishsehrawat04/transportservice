<?php

namespace Tests\Unit;

use App\Models\Warehouse;
use App\Models\WarehouseCartItem;
use App\Services\WarehousePricingService;
use PHPUnit\Framework\TestCase;

class WarehousePricingServiceTest extends TestCase
{
    public function test_it_charges_by_weight_when_weight_is_the_bigger_figure(): void
    {
        $service = new WarehousePricingService();
        $warehouse = new Warehouse([
            'price_per_day_per_kg' => 30,
            'min_charge' => 100,
        ]);

        // actual weight = 4 * 2 = 8kg, no dimensions so volumetric weight = 0.
        // charge = 8 * 30 * 1 day = 240, above min_charge so it stays 240.
        $result = $service->calculate([
            'quantity' => 2,
            'weight_kg' => 4,
            'storage_days' => 1,
            'tax_amount' => 50,
            'discount_amount' => 25,
        ], $warehouse);

        $this->assertSame('weight', $result['charge_basis']);
        $this->assertSame(8.0, $result['charge_weight_kg']);
        $this->assertSame(240.0, $result['weight_charge']);
        $this->assertSame(0.0, $result['volume_charge']);
        $this->assertSame(240.0, $result['subtotal']);
        $this->assertSame(265.0, $result['total_payment']);
    }

    public function test_it_charges_by_volumetric_weight_when_volume_is_the_bigger_figure(): void
    {
        $service = new WarehousePricingService();
        $warehouse = new Warehouse([
            'price_per_day_per_kg' => 10,
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
            'storage_days' => 1,
        ], $warehouse);

        $this->assertSame('volume', $result['charge_basis']);
        $this->assertSame(50.0, $result['volumetric_weight_kg']);
        $this->assertSame(50.0, $result['charge_weight_kg']);
        $this->assertSame(500.0, $result['volume_charge']);
        $this->assertSame(0.0, $result['weight_charge']);
        $this->assertSame(500.0, $result['total_payment']);
    }

    public function test_calculate_never_floors_a_single_items_price(): void
    {
        $service = new WarehousePricingService();
        $warehouse = new Warehouse([
            'price_per_day_per_kg' => 10,
            'min_charge' => 500,
        ]);

        // chargeable weight = 3kg, raw charge = 30 — min_charge is a
        // request-level floor, so a single item's own price stays raw.
        $result = $service->calculate([
            'quantity' => 1,
            'weight_kg' => 3,
            'storage_days' => 1,
        ], $warehouse);

        $this->assertSame(30.0, $result['subtotal']);
        $this->assertSame(30.0, $result['total_payment']);
    }

    public function test_floor_shipment_total_raises_a_combined_total_below_min_charge(): void
    {
        $service = new WarehousePricingService();
        $warehouse = new Warehouse([
            'price_per_day_per_kg' => 500,
        ]);

        // Two items, each individually below the min_charge, combine to
        // a request total that is still below it — the floor is applied
        // once to that combined total, not to each item.
        $itemA = $service->calculate(['quantity' => 1, 'weight_kg' => 1, 'storage_days' => 1], $warehouse);
        $itemB = $service->calculate(['quantity' => 1, 'weight_kg' => 1, 'storage_days' => 1], $warehouse);
        $combined = $itemA['total_payment'] + $itemB['total_payment'];

        $this->assertSame(1000.0, $combined);
        $this->assertSame(1500.0, $service->floorShipmentTotal($combined, new Warehouse(['min_charge' => 1500])));
    }

    public function test_floor_shipment_total_leaves_a_combined_total_above_min_charge_untouched(): void
    {
        $service = new WarehousePricingService();

        $this->assertSame(1000.0, $service->floorShipmentTotal(1000.0, new Warehouse(['min_charge' => 500])));
    }

    public function test_item_total_above_min_charge_is_used_as_is(): void
    {
        $service = new WarehousePricingService();
        $warehouse = new Warehouse([
            'price_per_day_per_kg' => 10,
            'min_charge' => 50,
        ]);

        // chargeable weight = 20kg, raw charge = 200, above the 50 min_charge floor.
        $result = $service->calculate([
            'quantity' => 1,
            'weight_kg' => 20,
            'storage_days' => 1,
        ], $warehouse);

        $this->assertSame(200.0, $result['subtotal']);
        $this->assertSame(200.0, $result['total_payment']);
    }

    public function test_it_calculates_volumetric_weight_from_centimeters_in_kg_not_cft(): void
    {
        $service = new WarehousePricingService();
        $cartItem = new WarehouseCartItem([
            'quantity' => 1,
            'length_cm' => 100,
            'width_cm' => 100,
            'height_cm' => 100,
            'weight_kg' => 10,
            'storage_days' => 1,
        ]);
        $warehouse = new Warehouse([
            'price_per_day_per_kg' => 0,
            'min_charge' => 0,
        ]);

        $result = $service->calculateCartItem($cartItem, $warehouse);

        // 100 * 100 * 100 = 1,000,000 cm3 / 4500 = 222.22kg volumetric weight.
        $this->assertSame(222.22, $result['volumetric_weight_kg']);
        $this->assertSame('volume', $result['charge_basis']);
        $this->assertSame(35.31, $result['volume_cft']);
    }

    public function test_storage_days_multiplies_the_chargeable_weight_correctly(): void
    {
        $service = new WarehousePricingService();
        $warehouse = new Warehouse([
            'price_per_day_per_kg' => 5,
            'min_charge' => 0,
        ]);

        $oneDay = $service->calculate(['quantity' => 1, 'weight_kg' => 10, 'storage_days' => 1], $warehouse);
        $sevenDays = $service->calculate(['quantity' => 1, 'weight_kg' => 10, 'storage_days' => 7], $warehouse);

        $this->assertSame(50.0, $oneDay['total_payment']);
        $this->assertSame(350.0, $sevenDays['total_payment']);
    }
}
