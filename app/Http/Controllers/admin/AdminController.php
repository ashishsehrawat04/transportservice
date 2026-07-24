<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CityRoute;
use App\Models\PricingSetting;
use App\Models\TransportAuthSetting;
use App\Models\TransportLead;
use App\Models\TransportQuote;
use App\Models\TransportServicePrice;
use App\Models\TransportAddress;
use App\Models\TransportCartItem;
use App\Models\ShipmentPayment;
use App\Models\Warehouse;
use App\Models\WarehouseAddress;
use App\Models\WarehouseCartItem;
use App\Models\WarehouseLead;
use App\Models\WarehousePayment;
use App\Models\WarehouseQuote;
use App\Services\ShipmentInvoicePdfService;
use App\Services\ShipmentPricingService;
use App\Services\TransportQuotePdfService;
use App\Services\WarehouseInvoicePdfService;
use App\Services\WarehousePricingService;
use App\Services\WarehouseQuotePdfService;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function __construct(
        private ShipmentPricingService $pricingService,
        private WarehousePricingService $warehousePricingService
    ) {
    }

    public function AdminDashboard()
    {

        $leadStatusCounts = TransportLead::selectRaw('admin_status, COUNT(*) as total')
            ->groupBy('admin_status')
            ->pluck('total', 'admin_status');

        $paymentStatusCounts = TransportLead::selectRaw('payment_status, COUNT(*) as total')
            ->groupBy('payment_status')
            ->pluck('total', 'payment_status');

        $monthlyLabels = [];
        $monthlyLeadCounts = [];
        $monthlyRevenue = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyLabels[] = $date->format('M Y');
            $monthlyLeadCounts[] = TransportLead::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $monthlyRevenue[] = (float) ShipmentPayment::where('status', 'success')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');
        }

        $dashboard = [
            'totalUsers' => User::where('role', 'user')->count(),
            'pendingUsers' => User::where('role', 'user')->where('status', 'pending')->count(),
            'totalRoutes' => CityRoute::count(),
            'activeRoutes' => CityRoute::where('is_active', true)->count(),
            'totalLeads' => TransportLead::count(),
            'pendingLeads' => (int) ($leadStatusCounts['pending'] ?? 0),
            'approvedLeads' => (int) ($leadStatusCounts['approved'] ?? 0),
            'deliveredLeads' => (int) ($leadStatusCounts['delivered'] ?? 0),
            'paidLeads' => (int) ($paymentStatusCounts['paid'] ?? 0),
            'unpaidLeads' => (int) ($paymentStatusCounts['unpaid'] ?? 0),
            'totalRevenue' => (float) ShipmentPayment::where('status', 'success')->sum('amount'),
            'todayRevenue' => (float) ShipmentPayment::where('status', 'success')->whereDate('created_at', today())->sum('amount'),
        ];



        $recentLeads = TransportLead::with(['user:id,name,email,mobile', 'cityRoute'])
            ->latest()
            ->limit(8)
            ->get();
        $recentPayments = ShipmentPayment::with(['user:id,name,email,mobile'])
            ->latest()
            ->limit(8)
            ->get();
        $recentUsers = User::latest()
            ->limit(6)
            ->get();




        return view('admin.dashboard', compact('dashboard', 'recentLeads', 'recentPayments', 'recentUsers', 'monthlyLabels', 'monthlyLeadCounts', 'monthlyRevenue'));
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


    // dd($request->all());
        $cityRoute = $id ? CityRoute::find($id) : null;

        if ($id && !$cityRoute) {
            return redirect()->route('admin.city_routes')->with('error', 'City route not found');
        }

        $validated = $request->validate([
            'from_city' => ['required','string', 'max:255',
                            Rule::unique('city_routes')->where(function ($query) use ($request) {
                                return $query->where('to_city', $request->to_city);
                            })->ignore($cityRoute?->id),],
            'to_city' => ['required', 'string', 'max:255'],
            'rate_per_weight' => ['required', 'numeric', 'min:0'],
            'transit_days' => ['required', 'integer', 'in:1,2,3'],
            'min_charge' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);



        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

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

    public function AdminWarehouses()
    {
        return view('admin.warehouse.index');
    }

    public function AdminManageWarehouse($id = null)
    {
        $warehouse = $id ? Warehouse::find($id) : new Warehouse();

        if ($id && !$warehouse) {
            return redirect()->route('admin.warehouses')->with('error', 'Warehouse not found');
        }

        return view('admin.warehouse.manage', compact('warehouse'));
    }

    public function AdminSaveWarehouse(Request $request, $id = null)
    {
        $warehouse = $id ? Warehouse::find($id) : null;

        if ($id && !$warehouse) {
            return redirect()->route('admin.warehouses')->with('error', 'Warehouse not found');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255',
                        Rule::unique('warehouses')->where(function ($query) use ($request) {
                            return $query->where('city', $request->city);
                        })->ignore($warehouse?->id),],
            'city' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:2000'],
            'price_per_day_per_kg' => ['required', 'numeric', 'min:0'],
            'min_charge' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        if ($warehouse) {
            $warehouse->update($validated);
            $message = 'Warehouse updated successfully';
        } else {
            Warehouse::create($validated);
            $message = 'Warehouse added successfully';
        }

        return redirect()->route('admin.warehouses')->with('success', $message);
    }

    public function AdminDeleteWarehouse($id)
    {
        $warehouse = Warehouse::find($id);

        if ($warehouse) {
            $warehouse->delete();
            return redirect()->route('admin.warehouses')->with('success', 'Warehouse deleted successfully');
        }

        return redirect()->route('admin.warehouses')->with('error', 'Warehouse not found');
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

            'address_line_1' => ['nullable', 'string', 'max:1000'],
            'address_line_2' => ['nullable', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'pincode' => ['nullable', 'digits_between:5,6'],

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
            'calculation_type' => ['required', Rule::in(['distance', 'volume'])],
            'weight_rate_per_kg' => ['nullable', 'numeric', 'min:0'],
            'volume_rate_per_cft' => ['nullable', 'numeric', 'min:0'],
            'distance_rate_per_km' => ['nullable', 'numeric', 'min:0'],
            'min_charge' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['base_price'] = 0;
        $validated['weight_rate_per_kg'] = $validated['weight_rate_per_kg'] ?? 0;
        $validated['volume_rate_per_cft'] = $validated['volume_rate_per_cft'] ?? 0;
        $validated['distance_rate_per_km'] = $validated['distance_rate_per_km'] ?? 0;
        $validated['multiplier'] = 1;
        $validated['max_charge'] = null;
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

    public function AdminWarehouseLeads()
    {
        return view('admin.warehouse.leads');
    }

    public function AdminManageWarehouseLead($id = null)
    {
        $warehouseLead = $id ? WarehouseLead::find($id) : new WarehouseLead();

        if ($id && !$warehouseLead) {
            return redirect()->route('admin.warehouse_leads')->with('error', 'Warehouse lead not found');
        }

        $users = User::orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)
            ->orderBy('city')
            ->orderBy('name')
            ->get();

        return view('admin.warehouse.manage-lead', compact('warehouseLead', 'users', 'warehouses'));
    }

    public function AdminSaveWarehouseLead(Request $request, $id = null)
    {
        $warehouseLead = $id ? WarehouseLead::find($id) : null;

        if ($id && !$warehouseLead) {
            return redirect()->route('admin.warehouse_leads')->with('error', 'Warehouse lead not found');
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
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'storage_days' => ['required', 'integer', 'min:1'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'requested_pickup_date' => ['required', 'date'],
            'admin_status' => ['required', Rule::in(['pending', 'reviewed', 'approved', 'dispatched', 'delivered', 'cancelled', 'rejected'])],
            'admin_description' => ['nullable', 'string'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'user_status' => ['required', Rule::in(['pending', 'confirmed', 'in_transit', 'delivered', 'cancelled'])],
            'payment_status' => ['required', Rule::in(['unpaid', 'partial', 'paid', 'refunded'])],
            'payment_method' => ['nullable', Rule::in(['cash', 'online', 'upi', 'bank_transfer'])],
            'transaction_id' => ['nullable', 'string', 'max:255'],
            'special_instructions' => ['nullable', 'string'],
        ]);

        $warehouse = Warehouse::where('is_active', true)->find($validated['warehouse_id']);

        if (!$warehouse) {
            return back()->withInput()->with('error', 'Please select an active warehouse first');
        }

        $breakdown = $this->warehousePricingService->calculateFromDimensions($validated, $warehouse);
        // This lead is a single-item storage request on its own, so the
        // warehouse's min_charge floor applies directly to its total here.
        $breakdown['total_payment'] = $this->warehousePricingService->floorShipmentTotal($breakdown['total_payment'], $warehouse);
        $leadPricing = array_intersect_key($breakdown, array_flip([
            'volume_cft', 'calculation_type', 'base_price',
            'weight_charge', 'volume_charge',
            'multiplier_applied', 'subtotal', 'tax_amount', 'discount_amount',
            'total_payment',
        ]));
        $validated = array_merge($validated, $leadPricing);

        if (!$warehouseLead) {
            $validated['tracking_number'] = $this->generateWarehouseTrackingNumber();
        }

        $oldPaymentStatus = $warehouseLead?->payment_status;
        $oldTransactionId = $warehouseLead?->transaction_id;

        if ($warehouseLead) {
            $warehouseLead->update($validated);
            $message = 'Warehouse lead updated successfully';
        } else {
            $warehouseLead = WarehouseLead::create($validated);
            $message = 'Warehouse lead added successfully';
        }

        $this->recordWarehouseLeadPaymentIfNeeded($warehouseLead->fresh(), $oldPaymentStatus, $oldTransactionId);
        WarehouseQuote::syncFromLead($warehouseLead->fresh(['user', 'warehouse', 'latestPayment']));

        // Once admin moves the lead past pending/reviewed, the cart items that
        // were awaiting this decision are done — drop them so they stop
        // showing up in the customer's storage cart.
        if (!in_array($warehouseLead->admin_status, ['pending', 'reviewed'])) {
            WarehouseCartItem::where('warehouse_lead_id', $warehouseLead->id)->delete();
        }

        return redirect()->route('admin.warehouse_leads')->with('success', $message);
    }

    public function AdminDeleteWarehouseLead($id)
    {
        $warehouseLead = WarehouseLead::find($id);

        if ($warehouseLead) {
            $warehouseLead->delete();
            return redirect()->route('admin.warehouse_leads')->with('success', 'Warehouse lead deleted successfully');
        }

        return redirect()->route('admin.warehouse_leads')->with('error', 'Warehouse lead not found');
    }

    public function AdminViewWarehouseLeadQuote($id)
    {
        $warehouseLead = WarehouseLead::with(['user', 'warehouse', 'latestPayment'])->find($id);

        if (!$warehouseLead) {
            return redirect()->route('admin.warehouse_leads')->with('error', 'Warehouse lead not found');
        }

        $quote = WarehouseQuote::syncFromLead($warehouseLead);
        $quote->load('user');
        $warehouseAddress = $this->warehouseAddressForQuote($quote);

        return view('admin.warehouse.quote-show', compact('quote', 'warehouseAddress'));
    }

    public function AdminDownloadWarehouseLeadQuote($id, WarehouseQuotePdfService $quotePdfService)
    {
        $warehouseLead = WarehouseLead::with(['user', 'warehouse', 'latestPayment'])->find($id);

        if (!$warehouseLead) {
            return redirect()->route('admin.warehouse_leads')->with('error', 'Warehouse lead not found');
        }

        $quote = WarehouseQuote::syncFromLead($warehouseLead);
        $quote->load('user');
        $fileName = ($quote->invoice_number ?: $quote->tracking_number ?: 'warehouse-quote-' . $quote->id);

        $result = $quotePdfService->output($quote, $this->warehouseAddressForQuote($quote));

        if (is_array($result) && isset($result['content'], $result['mimetype'])) {
            $content = $result['content'];
            $mimetype = $result['mimetype'];
        } else {
            $content = is_string($result) ? $result : '';
            $mimetype = 'application/pdf';
        }

        $ext = $mimetype === 'text/html' ? 'html' : 'pdf';

        return response($content, 200, [
            'Content-Type' => $mimetype,
            'Content-Disposition' => 'attachment; filename="' . $fileName . '.' . $ext . '"',
        ]);
    }

    public function AdminDownloadWarehouseLeadInvoice($id, WarehouseInvoicePdfService $invoicePdfService)
    {
        $warehouseLead = WarehouseLead::with(['user', 'warehouse', 'latestPayment'])->find($id);

        if (!$warehouseLead) {
            return redirect()->route('admin.warehouse_leads')->with('error', 'Warehouse lead not found');
        }

        if (!$this->warehouseInvoiceCanBeDownloaded($warehouseLead)) {
            return redirect()->route('admin.warehouse_leads')->with('error', 'Mark storage request as stored before downloading invoice.');
        }

        $payment = $warehouseLead->latestPayment ?: $this->createWarehouseInvoicePayment($warehouseLead);
        WarehouseQuote::syncFromLead($warehouseLead, $payment->invoice_number);
        $fileName = ($payment->invoice_number ?: $warehouseLead->tracking_number) . '.pdf';

        return response($invoicePdfService->output($warehouseLead, $payment), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    public function AdminPayments()
    {
        $paymentStatusCounts = ShipmentPayment::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $paymentStats = [
            'totalPayments' => ShipmentPayment::count(),
            'successPayments' => (int) ($paymentStatusCounts['success'] ?? 0),
            'pendingPayments' => (int) ($paymentStatusCounts['pending'] ?? 0),
            'refundedPayments' => (int) ($paymentStatusCounts['refunded'] ?? 0),
            'failedPayments' => (int) ($paymentStatusCounts['failed'] ?? 0),
            'totalRevenue' => (float) ShipmentPayment::where('status', 'success')->sum('amount'),
            'todayRevenue' => (float) ShipmentPayment::where('status', 'success')->whereDate('created_at', today())->sum('amount'),
        ];

        $warehousePaymentStatusCounts = WarehousePayment::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $warehousePaymentStats = [
            'totalPayments' => WarehousePayment::count(),
            'successPayments' => (int) ($warehousePaymentStatusCounts['success'] ?? 0),
            'pendingPayments' => (int) ($warehousePaymentStatusCounts['pending'] ?? 0),
            'refundedPayments' => (int) ($warehousePaymentStatusCounts['refunded'] ?? 0),
            'failedPayments' => (int) ($warehousePaymentStatusCounts['failed'] ?? 0),
            'totalRevenue' => (float) WarehousePayment::where('status', 'success')->sum('amount'),
            'todayRevenue' => (float) WarehousePayment::where('status', 'success')->whereDate('created_at', today())->sum('amount'),
        ];

        return view('admin.payments', compact('paymentStats', 'warehousePaymentStats'));
    }

    public function AdminTransportQuotes()
    {
        return view('admin.transport-quotes');
    }

    public function AdminViewTransportQuote($id)
    {
        $quote = TransportQuote::with(['user', 'transportLead.cityRoute'])->find($id);

        if (!$quote) {
            return redirect()->route('admin.transport_quotes')->with('error', 'Transport quote not found');
        }

        $transportAddress = $this->transportAddressForQuote($quote);

        return view('admin.transport-quote-show', compact('quote', 'transportAddress'));
    }

    public function AdminDownloadTransportQuote($id, TransportQuotePdfService $quotePdfService)
    {
        $quote = TransportQuote::with(['user', 'transportLead.cityRoute'])->find($id);

        if (!$quote) {
            return redirect()->route('admin.transport_quotes')->with('error', 'Transport quote not found');
        }

        $fileName = ($quote->invoice_number ?: $quote->tracking_number ?: 'transport-quote-' . $quote->id);

        $result = $quotePdfService->output($quote, $this->transportAddressForQuote($quote));

        if (is_array($result) && isset($result['content'], $result['mimetype'])) {
            $content = $result['content'];
            $mimetype = $result['mimetype'];
        } else {
            $content = is_string($result) ? $result : '';
            $mimetype = 'application/pdf';
        }

        $ext = $mimetype === 'text/html' ? 'html' : 'pdf';

        return response($content, 200, [
            'Content-Type' => $mimetype,
            'Content-Disposition' => 'attachment; filename="' . $fileName . '.' . $ext . '"',
        ]);
    }

    public function AdminManageTransportLead($id = null)
    {
        $transportLead = $id ? TransportLead::find($id) : new TransportLead();

        if ($id && !$transportLead) {
            return redirect()->route('admin.transport_leads')->with('error', 'Transport lead not found');
        }

        $users = User::orderBy('name')->get();
        $cityRoutes = CityRoute::where('is_active', true)
            ->orderBy('from_city')
            ->orderBy('to_city')
            ->get();
        $quoteMode = request()->boolean('quote');

        return view('admin.manage-transport-lead', compact('transportLead', 'users', 'cityRoutes', 'quoteMode'));
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
            'city_route_id' => ['required', 'exists:city_routes,id'],
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

        $route = CityRoute::where('is_active', true)->find($validated['city_route_id']);

        if (!$route) {
            return back()->withInput()->with('error', 'Please select an active city route first');
        }

        $breakdown = $this->pricingService->calculateFromDimensions($validated, $route);
        // This lead is a single-item shipment on its own, so the route's
        // min_charge floor applies directly to its total here.
        $breakdown['total_payment'] = $this->pricingService->floorShipmentTotal($breakdown['total_payment'], $route);
        $leadPricing = array_intersect_key($breakdown, array_flip([
            'volume_cft', 'calculation_type', 'base_price',
            'weight_charge', 'volume_charge', 'distance_charge',
            'multiplier_applied', 'subtotal', 'tax_amount', 'discount_amount',
            'total_payment',
        ]));
        $validated = array_merge($validated, $leadPricing);

        if (!$transportLead) {
            $validated['tracking_number'] = $this->generateTrackingNumber();
        }

        $oldPaymentStatus = $transportLead?->payment_status;
        $oldTransactionId = $transportLead?->transaction_id;

        if ($transportLead) {
            $transportLead->update($validated);
            $message = 'Transport lead updated successfully';
        } else {
            $transportLead = TransportLead::create($validated);
            $message = 'Transport lead added successfully';
        }

        $this->recordLeadPaymentIfNeeded($transportLead->fresh(), $oldPaymentStatus, $oldTransactionId);
        TransportQuote::syncFromLead($transportLead->fresh(['user', 'cityRoute', 'latestPayment']));

        // Once admin moves the lead past pending/reviewed (approved, rejected, dispatched,
        // delivered, cancelled), the cart items that were awaiting this decision are done —
        // drop them so they stop showing up in the customer's shipment cart.
        if (!in_array($transportLead->admin_status, ['pending', 'reviewed'])) {
            TransportCartItem::where('transport_lead_id', $transportLead->id)->delete();
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

    public function AdminViewTransportLeadQuote($id)
    {
        $transportLead = TransportLead::with(['user', 'cityRoute', 'latestPayment'])->find($id);

        if (!$transportLead) {
            return redirect()->route('admin.transport_leads')->with('error', 'Transport lead not found');
        }

        $quote = TransportQuote::syncFromLead($transportLead);
        $quote->load(['user', 'transportLead.cityRoute']);
        $transportAddress = $this->transportAddressForQuote($quote);

        return view('admin.transport-quote-show', compact('quote', 'transportAddress'));
    }

    public function AdminDownloadTransportLeadQuote($id, TransportQuotePdfService $quotePdfService)
    {
        $transportLead = TransportLead::with(['user', 'cityRoute', 'latestPayment'])->find($id);

        if (!$transportLead) {
            return redirect()->route('admin.transport_leads')->with('error', 'Transport lead not found');
        }

        $quote = TransportQuote::syncFromLead($transportLead);
        $quote->load(['user', 'transportLead.cityRoute']);
        $fileName = ($quote->invoice_number ?: $quote->tracking_number ?: 'transport-quote-' . $quote->id);

        $result = $quotePdfService->output($quote, $this->transportAddressForQuote($quote));

        if (is_array($result) && isset($result['content'], $result['mimetype'])) {
            $content = $result['content'];
            $mimetype = $result['mimetype'];
        } else {
            $content = is_string($result) ? $result : '';
            $mimetype = 'application/pdf';
        }

        $ext = $mimetype === 'text/html' ? 'html' : 'pdf';

        return response($content, 200, [
            'Content-Type' => $mimetype,
            'Content-Disposition' => 'attachment; filename="' . $fileName . '.' . $ext . '"',
        ]);
    }

    public function AdminDownloadTransportLeadInvoice($id, ShipmentInvoicePdfService $invoicePdfService)
    {
        $transportLead = TransportLead::with(['user', 'cityRoute', 'latestPayment'])->find($id);

        if (!$transportLead) {
            return redirect()->route('admin.transport_leads')->with('error', 'Transport lead not found');
        }

        if (!$this->invoiceCanBeDownloaded($transportLead)) {
            return redirect()->route('admin.transport_leads')->with('error', 'Mark shipment as delivered before downloading invoice.');
        }

        $payment = $transportLead->latestPayment ?: $this->createInvoicePayment($transportLead);
        TransportQuote::syncFromLead($transportLead, $payment->invoice_number);
        $fileName = ($payment->invoice_number ?: $transportLead->tracking_number) . '.pdf';

        return response($invoicePdfService->output($transportLead, $payment), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
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

    public function AdminPricingSettings()
    {
        $settings = PricingSetting::current();

        return view('admin.pricing-settings', compact('settings'));
    }

    public function AdminSavePricingSettings(Request $request)
    {
        $request->validate([
            'gst_percent' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        PricingSetting::current()->update([
            'gst_percent' => $request->gst_percent,
        ]);

        return redirect()->route('admin.pricing_settings')->with('success', 'Pricing settings updated successfully');
    }

    private function generateTrackingNumber(): string
    {
        do {
            $trackingNumber = 'TL-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (TransportLead::where('tracking_number', $trackingNumber)->exists());

        return $trackingNumber;
    }

    private function recordLeadPaymentIfNeeded(TransportLead $lead, ?string $oldPaymentStatus, ?string $oldTransactionId): void
    {
        if (!in_array($lead->payment_status, ['partial', 'paid', 'refunded'])) {
            return;
        }

        $paymentChanged = $oldPaymentStatus !== $lead->payment_status;
        $transactionChanged = $lead->transaction_id && $oldTransactionId !== $lead->transaction_id;

        if (!$paymentChanged && !$transactionChanged) {
            return;
        }

        $this->createInvoicePayment($lead);
    }

    private function createInvoicePayment(TransportLead $lead): ShipmentPayment
    {
        $payment = ShipmentPayment::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'user_id' => $lead->user_id,
            'transport_lead_id' => $lead->id,
            'amount' => $lead->total_payment,
            'method' => $lead->payment_method ?: 'cash',
            'status' => match ($lead->payment_status) {
                'paid' => 'success',
                'refunded' => 'refunded',
                default => 'pending',
            },
            'transaction_id' => $lead->transaction_id,
            'notes' => 'Payment status updated from lead admin panel.',
        ]);

        TransportQuote::syncFromLead($lead, $payment->invoice_number);

        return $payment;
    }

    private function invoiceCanBeDownloaded(TransportLead $lead): bool
    {
        return $lead->admin_status === 'delivered';
    }

    private function generateInvoiceNumber(): string
    {
        do {
            $invoiceNumber = 'INV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (ShipmentPayment::where('invoice_number', $invoiceNumber)->exists());

        return $invoiceNumber;
    }

    private function transportAddressForQuote(TransportQuote $quote): ?object
    {
        $snapshot = $quote->quote_data['transport_address'] ?? null;

        if (is_array($snapshot)) {
            return (object) $snapshot;
        }

        return TransportAddress::where('user_id', $quote->user_id)
            ->latest()
            ->first();
    }

    private function generateWarehouseTrackingNumber(): string
    {
        do {
            $trackingNumber = 'WH-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (WarehouseLead::where('tracking_number', $trackingNumber)->exists());

        return $trackingNumber;
    }

    private function recordWarehouseLeadPaymentIfNeeded(WarehouseLead $lead, ?string $oldPaymentStatus, ?string $oldTransactionId): void
    {
        if (!in_array($lead->payment_status, ['partial', 'paid', 'refunded'])) {
            return;
        }

        $paymentChanged = $oldPaymentStatus !== $lead->payment_status;
        $transactionChanged = $lead->transaction_id && $oldTransactionId !== $lead->transaction_id;

        if (!$paymentChanged && !$transactionChanged) {
            return;
        }

        $this->createWarehouseInvoicePayment($lead);
    }

    private function createWarehouseInvoicePayment(WarehouseLead $lead): WarehousePayment
    {
        $payment = WarehousePayment::create([
            'invoice_number' => $this->generateWarehouseInvoiceNumber(),
            'user_id' => $lead->user_id,
            'warehouse_lead_id' => $lead->id,
            'amount' => $lead->total_payment,
            'method' => $lead->payment_method ?: 'cash',
            'status' => match ($lead->payment_status) {
                'paid' => 'success',
                'refunded' => 'refunded',
                default => 'pending',
            },
            'transaction_id' => $lead->transaction_id,
            'notes' => 'Payment status updated from warehouse lead admin panel.',
        ]);

        WarehouseQuote::syncFromLead($lead, $payment->invoice_number);

        return $payment;
    }

    private function warehouseInvoiceCanBeDownloaded(WarehouseLead $lead): bool
    {
        return $lead->admin_status === 'delivered';
    }

    private function generateWarehouseInvoiceNumber(): string
    {
        do {
            $invoiceNumber = 'WINV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (WarehousePayment::where('invoice_number', $invoiceNumber)->exists());

        return $invoiceNumber;
    }

    private function warehouseAddressForQuote(WarehouseQuote $quote): ?object
    {
        $snapshot = $quote->quote_data['warehouse_address'] ?? null;

        if (is_array($snapshot)) {
            return (object) $snapshot;
        }

        return WarehouseAddress::where('user_id', $quote->user_id)
            ->latest()
            ->first();
    }

}
