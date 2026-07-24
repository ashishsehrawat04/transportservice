<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\ApiController;
use App\Http\Controllers\web\WebController;
use App\Http\Controllers\web\WarehouseController;


Route::get('/', function () {
    return view('web.index');
});

Route::GET('/admin/city-routes', function () {
    return view('admin.city-routes');
})->middleware('auth')->name('admin.city_routes');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/login/email/send-otp', [AuthController::class, 'sendEmailOtp'])->name('login.email.send_otp');
Route::post('/login/email/verify', [AuthController::class, 'verifyEmailOtp'])->name('login.email.verify');
Route::post('/login/mobile/send-otp', [AuthController::class, 'requestMobileOtp'])->name('login.mobile.send_otp');
Route::post('/login/mobile/verify', [AuthController::class, 'verifyMobileOtp'])->name('login.mobile.verify');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

Route::middleware('auth')->group(function () {
    Route::get('/user-profile', [WebController::class, 'UserProfile'])->name('user.profile');
    Route::get('/user-profile-edit', [WebController::class, 'UserProfileEdit'])->name('user.profile.edit');
    Route::post('/user-profile-update', [WebController::class, 'UpdateUserProfile'])->name('user.profile.update');
});
Route::get('/shipment/add-item', [WebController::class, 'addShipmentItem'])->name('shipment.add_item');
Route::post('/shipment/add-item', [WebController::class, 'saveShipmentItems'])->name('shipment.save_items');
Route::post('/shipment/estimate-items', [WebController::class, 'estimateShipmentItems'])->name('shipment.estimate_items');
Route::get('/shipment/cart', [WebController::class, 'shipmentCart'])->name('shipment.cart');
Route::post('/shipment/cart/checkout', [WebController::class, 'checkoutShipmentCart'])->middleware('auth')->name('shipment.cart.checkout');
Route::post('/shipment/cart/checkout-one', [WebController::class, 'checkoutOneShipment'])->middleware('auth')->name('shipment.cart.checkout_one');
Route::get('/shipment/cart/edit/{id}', [WebController::class, 'editShipmentCartItem'])->name('shipment.cart.edit');
Route::post('/shipment/cart/update/{id}', [WebController::class, 'updateShipmentCartItem'])->name('shipment.cart.update');
Route::get('/shipment/cart/delete/{id}', [WebController::class, 'deleteShipmentCartItem'])->name('shipment.cart.delete');
Route::post('/shipment/cart/cancel/{leadId}', [WebController::class, 'cancelShipment'])->middleware('auth')->name('shipment.cart.cancel');
Route::post('/shipment/cart/cancel-fresh', [WebController::class, 'cancelFreshShipment'])->middleware('auth')->name('shipment.cart.cancel_fresh');
Route::get('/shipment/leads', [WebController::class, 'shipmentLeads'])->middleware('auth')->name('shipment.leads');
Route::get('/track-and-trace', [WebController::class, 'trackShipment'])->name('shipment.track');
Route::get('/track-and-trace/{trackingNumber}/invoice', [WebController::class, 'downloadShipmentInvoice'])->name('shipment.invoice.download');

Route::get('/warehouse/add-item', [WarehouseController::class, 'addWarehouseItem'])->name('warehouse.add_item');
Route::post('/warehouse/add-item', [WarehouseController::class, 'saveWarehouseItems'])->name('warehouse.save_items');
Route::post('/warehouse/estimate-items', [WarehouseController::class, 'estimateWarehouseItems'])->name('warehouse.estimate_items');
Route::get('/warehouse/cart', [WarehouseController::class, 'warehouseCart'])->name('warehouse.cart');
Route::post('/warehouse/cart/checkout', [WarehouseController::class, 'checkoutWarehouseCart'])->middleware('auth')->name('warehouse.cart.checkout');
Route::post('/warehouse/cart/checkout-one', [WarehouseController::class, 'checkoutOneWarehouse'])->middleware('auth')->name('warehouse.cart.checkout_one');
Route::get('/warehouse/cart/edit/{id}', [WarehouseController::class, 'editWarehouseCartItem'])->name('warehouse.cart.edit');
Route::post('/warehouse/cart/update/{id}', [WarehouseController::class, 'updateWarehouseCartItem'])->name('warehouse.cart.update');
Route::get('/warehouse/cart/delete/{id}', [WarehouseController::class, 'deleteWarehouseCartItem'])->name('warehouse.cart.delete');
Route::post('/warehouse/cart/cancel/{leadId}', [WarehouseController::class, 'cancelWarehouse'])->middleware('auth')->name('warehouse.cart.cancel');
Route::post('/warehouse/cart/cancel-fresh', [WarehouseController::class, 'cancelFreshWarehouse'])->middleware('auth')->name('warehouse.cart.cancel_fresh');
Route::get('/warehouse/leads', [WarehouseController::class, 'warehouseLeads'])->middleware('auth')->name('warehouse.leads');
Route::get('/track-storage', [WarehouseController::class, 'trackWarehouse'])->name('warehouse.track');
Route::get('/track-storage/{trackingNumber}/invoice', [WarehouseController::class, 'downloadWarehouseInvoice'])->name('warehouse.invoice.download');


// admin routes

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    Route::get('', [AdminController::class, 'AdminDashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'AdminUsers'])->name('admin.users');
    Route::get('/get-users', [ApiController::class, 'AdminGetUsers'])->name('adminget.users');
    Route::get('/get-users/{id}', [ApiController::class, 'AdminGetUserDetails'])->name('adminget.user.details');
    Route::get('/users/delete/{slug}', [AdminController::class, 'AdminDeleteUsers'])->name('admin.delete.users');
    Route::get('/users/edit/{slug}', [AdminController::class, 'AdminEditUsers'])->name('admin.edit.users');
    Route::post('/users/update/{slug}', [AdminController::class, 'AdminUpdateUsers'])->name('admin.update.users');

    Route::get('/city-routes/manage/{id?}', [AdminController::class, 'AdminManageCityRoute'])->name('admin.manage.city_route');
    Route::post('/city-routes/manage/{id?}', [AdminController::class, 'AdminSaveCityRoute'])->name('admin.save.city_route');
    Route::get('/city-routes/delete/{id}', [AdminController::class, 'AdminDeleteCityRoute'])->name('admin.delete.city_route');
    Route::get('/get-city-routes', [ApiController::class, 'AdminGetCityRoutes'])->name('adminget.city.routes');

    Route::get('/warehouses', [AdminController::class, 'AdminWarehouses'])->name('admin.warehouses');
    Route::get('/warehouses/manage/{id?}', [AdminController::class, 'AdminManageWarehouse'])->name('admin.manage.warehouse');
    Route::post('/warehouses/manage/{id?}', [AdminController::class, 'AdminSaveWarehouse'])->name('admin.save.warehouse');
    Route::get('/warehouses/delete/{id}', [AdminController::class, 'AdminDeleteWarehouse'])->name('admin.delete.warehouse');
    Route::get('/get-warehouses', [ApiController::class, 'AdminGetWarehouses'])->name('adminget.warehouses');

    Route::get('/warehouse-leads', [AdminController::class, 'AdminWarehouseLeads'])->name('admin.warehouse_leads');
    Route::get('/warehouse-leads/manage/{id?}', [AdminController::class, 'AdminManageWarehouseLead'])->name('admin.manage.warehouse_lead');
    Route::post('/warehouse-leads/manage/{id?}', [AdminController::class, 'AdminSaveWarehouseLead'])->name('admin.save.warehouse_lead');
    Route::get('/warehouse-leads/{id}/quote', [AdminController::class, 'AdminViewWarehouseLeadQuote'])->name('admin.warehouse_lead.quote');
    Route::get('/warehouse-leads/{id}/quote/download', [AdminController::class, 'AdminDownloadWarehouseLeadQuote'])->name('admin.warehouse_lead.quote.download');
    Route::get('/warehouse-leads/{id}/invoice', [AdminController::class, 'AdminDownloadWarehouseLeadInvoice'])->name('admin.warehouse_lead.invoice');
    Route::get('/warehouse-leads/delete/{id}', [AdminController::class, 'AdminDeleteWarehouseLead'])->name('admin.delete.warehouse_lead');
    Route::get('/get-warehouse-leads', [ApiController::class, 'AdminGetWarehouseLeads'])->name('adminget.warehouse_leads');
    Route::get('/get-warehouse-payments', [ApiController::class, 'AdminGetWarehousePayments'])->name('adminget.warehouse_payments');

    Route::get('/transport-prices', [AdminController::class, 'AdminTransportPrices'])->name('admin.transport_prices');
    Route::get('/transport-prices/manage/{id?}', [AdminController::class, 'AdminManageTransportPrice'])->name('admin.manage.transport_price');
    Route::post('/transport-prices/manage/{id?}', [AdminController::class, 'AdminSaveTransportPrice'])->name('admin.save.transport_price');
    Route::get('/transport-prices/delete/{id}', [AdminController::class, 'AdminDeleteTransportPrice'])->name('admin.delete.transport_price');
    Route::get('/get-transport-prices', [ApiController::class, 'AdminGetTransportPrices'])->name('adminget.transport.prices');

    Route::get('/transport-leads', [AdminController::class, 'AdminTransportLeads'])->name('admin.transport_leads');
    Route::get('/transport-leads/manage/{id?}', [AdminController::class, 'AdminManageTransportLead'])->name('admin.manage.transport_lead');
    Route::post('/transport-leads/manage/{id?}', [AdminController::class, 'AdminSaveTransportLead'])->name('admin.save.transport_lead');
    Route::get('/transport-leads/{id}/quote', [AdminController::class, 'AdminViewTransportLeadQuote'])->name('admin.transport_lead.quote');
    Route::get('/transport-leads/{id}/quote/download', [AdminController::class, 'AdminDownloadTransportLeadQuote'])->name('admin.transport_lead.quote.download');
    Route::get('/transport-leads/{id}/invoice', [AdminController::class, 'AdminDownloadTransportLeadInvoice'])->name('admin.transport_lead.invoice');
    Route::get('/transport-leads/delete/{id}', [AdminController::class, 'AdminDeleteTransportLead'])->name('admin.delete.transport_lead');
    Route::get('/get-transport-leads', [ApiController::class, 'AdminGetTransportLeads'])->name('adminget.transport.leads');

    Route::get('/transport-quotes', [AdminController::class, 'AdminTransportQuotes'])->name('admin.transport_quotes');
    Route::get('/transport-quotes/{id}', [AdminController::class, 'AdminViewTransportQuote'])->name('admin.transport_quotes.view');
    Route::get('/transport-quotes/{id}/download', [AdminController::class, 'AdminDownloadTransportQuote'])->name('admin.transport_quotes.download');
    Route::get('/get-transport-quotes', [ApiController::class, 'AdminGetTransportQuotes'])->name('adminget.transport.quotes');

    Route::get('/payments', [AdminController::class, 'AdminPayments'])->name('admin.payments');
    Route::get('/get-payments', [ApiController::class, 'AdminGetPayments'])->name('adminget.payments');

    Route::get('/auth-settings', [AdminController::class, 'AdminAuthSettings'])->name('admin.auth_settings');
    Route::post('/auth-settings', [AdminController::class, 'AdminSaveAuthSettings'])->name('admin.save.auth_settings');

    Route::get('/pricing-settings', [AdminController::class, 'AdminPricingSettings'])->name('admin.pricing_settings');
    Route::post('/pricing-settings', [AdminController::class, 'AdminSavePricingSettings'])->name('admin.save.pricing_settings');

});
