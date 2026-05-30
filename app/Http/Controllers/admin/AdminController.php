<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\City;
use App\Models\CityRoute;
use App\Models\TransportAuthSetting;
use App\Models\TransportLead;
use App\Models\TransportServicePrice;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function AdminDashboard()
    {
        return view('admin.dashboard');
    }

    public function AdminUsers()
    {
        return view('admin.users');
    }
    public function AdminDeleteUsers($slug)
    {
        $user = User::where('slug', $slug)->first();
        if ($user) {
            $user->delete();
            return redirect()->route('admin.users')->with('success', 'User deleted successfully');
        }
        return redirect()->route('admin.users')->with('error', 'User not found');
    }

    public function AdminManageCityRoute($id = null)
    {
        $cityRoute = $id ? CityRoute::find($id) : new CityRoute();

        if ($id && !$cityRoute) {
            return redirect()->route('admin.city_routes')->with('error', 'City route not found');
        }

        return view('admin.manage-city-route', compact('cityRoute'));
    }

    public function AdminSaveCityRoute(Request $request, $id = null)
    {
        $cityRoute = $id ? CityRoute::find($id) : null;

        if ($id && !$cityRoute) {
            return redirect()->route('admin.city_routes')->with('error', 'City route not found');
        }

        $validated = $request->validate([
            'from_city' => [
                'required',
                'string',
                'max:255',
                Rule::unique('city_routes')->where(function ($query) use ($request) {
                    return $query->where('to_city', $request->to_city);
                })->ignore($cityRoute?->id),
            ],
            'to_city' => ['required', 'string', 'max:255'],
            'distance_km' => ['required', 'numeric', 'min:0'],
            'base_rate_per_km' => ['required', 'numeric', 'min:0'],
            'min_charge' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        City::firstOrCreate(['name' => $validated['from_city']], ['is_active' => true]);
        City::firstOrCreate(['name' => $validated['to_city']], ['is_active' => true]);

        if ($cityRoute) {
            $cityRoute->update($validated);
            $message = 'City route updated successfully';
        } else {
            CityRoute::create($validated);
            $message = 'City route added successfully';
        }

        return redirect()->route('admin.city_routes')->with('success', $message);
    }

    public function AdminDeleteCityRoute($id)
    {
        $cityRoute = CityRoute::find($id);

        if ($cityRoute) {
            $cityRoute->delete();
            return redirect()->route('admin.city_routes')->with('success', 'City route deleted successfully');
        }

        return redirect()->route('admin.city_routes')->with('error', 'City route not found');
    }

    public function AdminEditUsers($slug)
    {
        $user = User::where('slug', $slug)->first();
        if ($user) {
            return view('admin.edit-user', compact('user'));
        }
        return redirect()->route('admin.users')->with('error', 'User not found');
    }


    public function AdminUpdateUsers(Request $request, $slug)
    {
        $user = User::where('slug', $slug)->first();

        if (!$user) {
            return redirect()
                ->route('admin.users')
                ->with('error', 'User not found');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',

            'email' => [
                'required','email', 'max:255',Rule::unique('users')->ignore($user->id),
            ],

            'mobile' => [
                'nullable',
                'digits_between:10,15',
                Rule::unique('users')->ignore($user->id),
            ],

            'role' => [
                'required',
                Rule::in(['user', 'admin']),
            ],

            'status' => [
                'required',
                Rule::in(['pending', 'approved', 'rejected', 'blocked']),
            ],
        ]);

        $user->update($validated);

        return redirect()
            ->route('admin.users')
            ->with('success', 'User updated successfully');
    }

    public function AdminTransportPrices()
    {
        return view('admin.trasnport-price');
    }

    public function AdminManageTransportPrice($id = null)
    {
        $transportPrice = $id ? TransportServicePrice::find($id) : new TransportServicePrice();

        if ($id && !$transportPrice) {
            return redirect()->route('admin.transport_prices')->with('error', 'Transport price not found');
        }

        return view('admin.manage-transport-price', compact('transportPrice'));
    }

    public function AdminSaveTransportPrice(Request $request, $id = null)
    {
        $transportPrice = $id ? TransportServicePrice::find($id) : null;

        if ($id && !$transportPrice) {
            return redirect()->route('admin.transport_prices')->with('error', 'Transport price not found');
        }

        $validated = $request->validate([
            'item_type' => [
                'required',
                'string',
                'max:255',
                Rule::unique('transport_service_prices')->ignore($transportPrice?->id),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'weight_rate_per_kg' => ['required', 'numeric', 'min:0'],
            'volume_rate_per_cft' => ['required', 'numeric', 'min:0'],
            'distance_rate_per_km' => ['required', 'numeric', 'min:0'],
            'multiplier' => ['required', 'numeric', 'min:0'],
            'min_charge' => ['required', 'numeric', 'min:0'],
            'max_charge' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        if ($transportPrice) {
            $transportPrice->update($validated);
            $message = 'Transport price updated successfully';
        } else {
            TransportServicePrice::create($validated);
            $message = 'Transport price added successfully';
        }

        return redirect()->route('admin.transport_prices')->with('success', $message);
    }

    public function AdminDeleteTransportPrice($id)
    {
        $transportPrice = TransportServicePrice::find($id);

        if ($transportPrice) {
            $transportPrice->delete();
            return redirect()->route('admin.transport_prices')->with('success', 'Transport price deleted successfully');
        }

        return redirect()->route('admin.transport_prices')->with('error', 'Transport price not found');
    }

    public function AdminTransportLeads()
    {
        return view('admin.transport-leads');
    }

    public function AdminManageTransportLead($id = null)
    {
        $transportLead = $id ? TransportLead::find($id) : new TransportLead();

        if ($id && !$transportLead) {
            return redirect()->route('admin.transport_leads')->with('error', 'Transport lead not found');
        }

        $this->syncCitiesFromRoutes();

        $users = User::orderBy('name')->get();
        $cities = City::where('is_active', true)->orderBy('name')->get();
        $servicePrice = TransportServicePrice::where('is_active', true)->orderBy('id')->first();

        return view('admin.manage-transport-lead', compact('transportLead', 'users', 'cities', 'servicePrice'));
    }

    public function AdminSaveTransportLead(Request $request, $id = null)
    {
        $transportLead = $id ? TransportLead::find($id) : null;

        if ($id && !$transportLead) {
            return redirect()->route('admin.transport_leads')->with('error', 'Transport lead not found');
        }

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'item_name' => ['required', 'string', 'max:255'],
            'item_type' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'length_cm' => ['nullable', 'numeric', 'min:0'],
            'width_cm' => ['nullable', 'numeric', 'min:0'],
            'height_cm' => ['nullable', 'numeric', 'min:0'],
            'weight_kg' => ['required', 'numeric', 'min:0.01'],
            'from_city_id' => ['required', 'exists:cities,id'],
            'to_city_id' => ['required', 'different:from_city_id', 'exists:cities,id'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'requested_pickup_date' => ['required', 'date'],
            'confirmed_pickup_date' => ['nullable', 'date'],
            'expected_delivery_date' => ['nullable', 'date'],
            'actual_delivery_date' => ['nullable', 'date'],
            'admin_status' => ['required', Rule::in(['pending', 'reviewed', 'approved', 'dispatched', 'delivered', 'cancelled', 'rejected'])],
            'admin_description' => ['nullable', 'string'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'user_status' => ['required', Rule::in(['pending', 'confirmed', 'in_transit', 'delivered', 'cancelled'])],
            'payment_status' => ['required', Rule::in(['unpaid', 'partial', 'paid', 'refunded'])],
            'payment_method' => ['nullable', Rule::in(['cash', 'online', 'upi', 'bank_transfer'])],
            'transaction_id' => ['nullable', 'string', 'max:255'],
            'special_instructions' => ['nullable', 'string'],
        ]);

        $price = TransportServicePrice::where('is_active', true)->orderBy('id')->first();

        if (!$price) {
            return back()->withInput()->with('error', 'Please add an active transport service price first');
        }

        $fromCity = City::findOrFail($validated['from_city_id']);
        $toCity = City::findOrFail($validated['to_city_id']);
        $route = CityRoute::where('is_active', true)
            ->where(function ($query) use ($fromCity, $toCity) {
                $query->where(function ($inner) use ($fromCity, $toCity) {
                    $inner->where('from_city', $fromCity->name)->where('to_city', $toCity->name);
                })->orWhere(function ($inner) use ($fromCity, $toCity) {
                    $inner->where('from_city', $toCity->name)->where('to_city', $fromCity->name);
                });
            })
            ->first();

        if (!$route) {
            return back()->withInput()->with('error', 'Please add an active city route for selected cities first');
        }

        $validated = array_merge($validated, $this->calculateTransportLeadPrice($validated, $price, $route));
        $validated['item_type'] = $validated['item_type'] ?: $price->item_type;

        if (!$transportLead) {
            $validated['tracking_number'] = $this->generateTrackingNumber();
        }

        if ($transportLead) {
            $transportLead->update($validated);
            $message = 'Transport lead updated successfully';
        } else {
            TransportLead::create($validated);
            $message = 'Transport lead added successfully';
        }

        return redirect()->route('admin.transport_leads')->with('success', $message);
    }

    public function AdminDeleteTransportLead($id)
    {
        $transportLead = TransportLead::find($id);

        if ($transportLead) {
            $transportLead->delete();
            return redirect()->route('admin.transport_leads')->with('success', 'Transport lead deleted successfully');
        }

        return redirect()->route('admin.transport_leads')->with('error', 'Transport lead not found');
    }

    public function AdminAuthSettings()
    {
        $settings = TransportAuthSetting::current();

        return view('admin.auth-settings', compact('settings'));
    }

    public function AdminSaveAuthSettings(Request $request)
    {
        $settings = TransportAuthSetting::current();

        $settings->update([
            'email_login_enabled' => $request->has('email_login_enabled'),
            'mobile_login_enabled' => $request->has('mobile_login_enabled'),
            'google_login_enabled' => $request->has('google_login_enabled'),
            'admin_approval_required' => $request->has('admin_approval_required'),
        ]);

        return redirect()->route('admin.auth_settings')->with('success', 'Auth settings updated successfully');
    }

    private function calculateTransportLeadPrice(array $data, TransportServicePrice $price, CityRoute $route): array
    {
        $quantity = (int) $data['quantity'];
        $volumeCft = 0;

        if (!empty($data['length_cm']) && !empty($data['width_cm']) && !empty($data['height_cm'])) {
            $volumeCft = round(((float) $data['length_cm'] * (float) $data['width_cm'] * (float) $data['height_cm']) / 28316.8466, 2);
        }

        $totalWeight = (float) $data['weight_kg'] * $quantity;
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

        $taxAmount = (float) ($data['tax_amount'] ?? 0);
        $discountAmount = (float) ($data['discount_amount'] ?? 0);

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

    private function generateTrackingNumber(): string
    {
        do {
            $trackingNumber = 'TL-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (TransportLead::where('tracking_number', $trackingNumber)->exists());

        return $trackingNumber;
    }

    private function syncCitiesFromRoutes(): void
    {
        CityRoute::query()
            ->select('from_city', 'to_city')
            ->get()
            ->each(function (CityRoute $route) {
                City::firstOrCreate(['name' => $route->from_city], ['is_active' => true]);
                City::firstOrCreate(['name' => $route->to_city], ['is_active' => true]);
            });
    }
}
