<?php

namespace Database\Seeders;

use App\Models\CityRoute;
use App\Models\ShipmentPayment;
use App\Models\TransportAuthSetting;
use App\Models\TransportCartItem;
use App\Models\TransportLead;
use App\Models\TransportServicePrice;
use App\Models\User;
use App\Services\ShipmentPricingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TransportDemoSeeder extends Seeder
{
    public function run(): void
    {
        TransportAuthSetting::current()->update([
            'email_login_enabled' => true,
            'mobile_login_enabled' => true,
            'google_login_enabled' => false,
            'admin_approval_required' => false,
        ]);

        $admin = User::updateOrCreate(
            ['email' => 'admin@transport.test'],
            [
                'name' => 'Demo Admin',
                'mobile' => '9000000001',
                'password' => Hash::make('password'),
                'slug' => 'demo-admin',
                'role' => 'admin',
                'status' => 'approved',
                'login_type' => 'email',
                'wallet_balance' => 0,
            ]
        );

        $user = User::updateOrCreate(
            ['email' => 'customer@transport.test'],
            [
                'name' => 'Demo Customer',
                'mobile' => '9000000002',
                'password' => Hash::make('password'),
                'slug' => 'demo-customer',
                'role' => 'user',
                'status' => 'approved',
                'login_type' => 'email',
                'wallet_balance' => 2500,
            ]
        );

        $staff = User::updateOrCreate(
            ['email' => 'driver@transport.test'],
            [
                'name' => 'Demo Driver',
                'mobile' => '9000000003',
                'password' => Hash::make('password'),
                'slug' => 'demo-driver',
                'role' => 'user',
                'status' => 'approved',
                'login_type' => 'email',
                'wallet_balance' => 0,
            ]
        );

        $routes = [
            ['from_city' => 'Delhi', 'to_city' => 'Jaipur', 'rate_per_weight' => 18, 'transit_days' => 1, 'min_charge' => 1200],
            ['from_city' => 'Delhi', 'to_city' => 'Gurugram', 'rate_per_weight' => 28, 'transit_days' => 1, 'min_charge' => 650],
            ['from_city' => 'Noida', 'to_city' => 'Mumbai', 'rate_per_weight' => 24, 'transit_days' => 3, 'min_charge' => 8000],
        ];

        foreach ($routes as $route) {
            CityRoute::updateOrCreate(
                [
                    'from_city' => $route['from_city'],
                    'to_city' => $route['to_city'],
                ],
                $route + ['is_active' => true]
            );
        }

        $prices = [
            [
                'item_type' => 'bike',
                'description' => 'Two wheeler transport',
                'base_price' => 1500,
                'weight_rate_per_kg' => 12,
                'volume_rate_per_cft' => 4,
                'distance_rate_per_km' => 16,
                'multiplier' => 1,
                'min_charge' => 1800,
                'max_charge' => 18000,
            ],
            [
                'item_type' => 'home_goods',
                'description' => 'Packed household goods',
                'base_price' => 900,
                'weight_rate_per_kg' => 18,
                'volume_rate_per_cft' => 12,
                'distance_rate_per_km' => 20,
                'multiplier' => 1.10,
                'min_charge' => 2500,
                'max_charge' => 45000,
            ],
            [
                'item_type' => 'electronics',
                'description' => 'Fragile electronics shipment',
                'base_price' => 700,
                'weight_rate_per_kg' => 22,
                'volume_rate_per_cft' => 15,
                'distance_rate_per_km' => 18,
                'multiplier' => 1.15,
                'min_charge' => 2000,
                'max_charge' => 30000,
            ],
        ];

        foreach ($prices as $price) {
            TransportServicePrice::updateOrCreate(
                ['item_type' => $price['item_type']],
                $price + ['is_active' => true]
            );
        }

        $this->seedCartItem($user);
        $this->seedLeads($user, $staff);

        $this->command?->info('Transport demo data seeded.');
        $this->command?->line('Admin login: admin@transport.test / password');
        $this->command?->line('Customer login: customer@transport.test / password');
    }

    private function seedCartItem(User $user): void
    {
        $route = CityRoute::where('from_city', 'Delhi')->where('to_city', 'Jaipur')->first();

        if (!$route) {
            return;
        }

        $cartItem = TransportCartItem::updateOrCreate(
            [
                'user_id' => $user->id,
                'item_name' => 'Demo Cart Sofa Set',
            ],
            [
                'guest_id' => null,
                'city_route_id' => $route->id,
                'item_type' => 'home_goods',
                'quantity' => 1,
                'length_cm' => 180,
                'width_cm' => 80,
                'height_cm' => 85,
                'weight_kg' => 65,
                'pickup_date' => now()->addDays(2)->toDateString(),
                'delivery_date' => now()->addDays(5)->toDateString(),
            ]
        );

        $cartItem->update([
            'estimated_total' => app(ShipmentPricingService::class)
                ->calculateCartItem($cartItem, $route)['total_payment'],
        ]);
    }

    private function seedLeads(User $user, User $staff): void
    {
        $leadRows = [
            [
                'tracking_number' => 'TL-DEMO-PAID',
                'item_name' => 'Royal Enfield Bike',
                'item_type' => 'bike',
                'quantity' => 1,
                'length_cm' => 210,
                'width_cm' => 80,
                'height_cm' => 120,
                'weight_kg' => 180,
                'from' => 'Delhi',
                'to' => 'Jaipur',
                'admin_status' => 'approved',
                'user_status' => 'confirmed',
                'payment_status' => 'paid',
                'payment_method' => 'upi',
                'transaction_id' => 'UPI-DEMO-1001',
                'tax_amount' => 250,
                'discount_amount' => 100,
            ],
            [
                'tracking_number' => 'TL-DEMO-PENDING',
                'item_name' => 'Household Boxes',
                'item_type' => 'home_goods',
                'quantity' => 6,
                'length_cm' => 60,
                'width_cm' => 45,
                'height_cm' => 45,
                'weight_kg' => 18,
                'from' => 'Delhi',
                'to' => 'Gurugram',
                'admin_status' => 'pending',
                'user_status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => null,
                'transaction_id' => null,
                'tax_amount' => 0,
                'discount_amount' => 0,
            ],
            [
                'tracking_number' => 'TL-DEMO-DISPATCHED',
                'item_name' => 'LED TV',
                'item_type' => 'electronics',
                'quantity' => 1,
                'length_cm' => 140,
                'width_cm' => 18,
                'height_cm' => 85,
                'weight_kg' => 22,
                'from' => 'Noida',
                'to' => 'Mumbai',
                'admin_status' => 'dispatched',
                'user_status' => 'in_transit',
                'payment_status' => 'partial',
                'payment_method' => 'online',
                'transaction_id' => 'PAY-DEMO-2001',
                'tax_amount' => 500,
                'discount_amount' => 0,
            ],
        ];

        foreach ($leadRows as $row) {
            $route = CityRoute::where('from_city', $row['from'])->where('to_city', $row['to'])->first();

            if (!$route) {
                continue;
            }

            $breakdown = app(ShipmentPricingService::class)->calculateFromDimensions($row, $route);
            // transport_leads only has the aggregate pricing columns — the cart-item-only
            // fields (charge_basis, charge_weight_kg, actual_weight_kg, volumetric_weight_kg)
            // don't exist on this table, so only merge in what it supports.
            $leadPricing = array_intersect_key($breakdown, array_flip([
                'volume_cft', 'calculation_type', 'base_price',
                'weight_charge', 'volume_charge', 'distance_charge',
                'multiplier_applied', 'subtotal', 'tax_amount', 'discount_amount',
                'total_payment',
            ]));

            $lead = TransportLead::updateOrCreate(
                ['tracking_number' => $row['tracking_number']],
                array_merge([
                    'user_id' => $user->id,
                    'item_name' => $row['item_name'],
                    'item_type' => $row['item_type'],
                    'quantity' => $row['quantity'],
                    'length_cm' => $row['length_cm'],
                    'width_cm' => $row['width_cm'],
                    'height_cm' => $row['height_cm'],
                    'weight_kg' => $row['weight_kg'],
                    'city_route_id' => $route->id,
                    'requested_pickup_date' => now()->addDays(1)->toDateString(),
                    'confirmed_pickup_date' => in_array($row['admin_status'], ['approved', 'dispatched', 'delivered']) ? now()->addDays(2)->toDateString() : null,
                    'expected_delivery_date' => now()->addDays(6)->toDateString(),
                    'actual_delivery_date' => $row['admin_status'] === 'delivered' ? now()->addDays(5)->toDateString() : null,
                    'admin_status' => $row['admin_status'],
                    'admin_description' => 'Demo CRM lead for testing.',
                    'assigned_to' => $staff->id,
                    'user_status' => $row['user_status'],
                    'payment_status' => $row['payment_status'],
                    'payment_method' => $row['payment_method'],
                    'transaction_id' => $row['transaction_id'],
                    'special_instructions' => 'Handle demo shipment carefully.',
                ], $leadPricing)
            );

            $this->seedPaymentForLead($lead);
        }
    }

    private function seedPaymentForLead(TransportLead $lead): void
    {
        if (!in_array($lead->payment_status, ['paid', 'partial'])) {
            return;
        }

        ShipmentPayment::updateOrCreate(
            ['invoice_number' => 'INV-' . str_replace('TL-', '', $lead->tracking_number)],
            [
                'user_id' => $lead->user_id,
                'transport_lead_id' => $lead->id,
                'amount' => $lead->payment_status === 'partial'
                    ? round((float) $lead->total_payment / 2, 2)
                    : $lead->total_payment,
                'method' => $lead->payment_method ?: 'cash',
                'status' => $lead->payment_status === 'paid' ? 'success' : 'pending',
                'transaction_id' => $lead->transaction_id,
                'notes' => 'Demo seeded payment.',
            ]
        );
    }
}
