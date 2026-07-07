<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\ApiController;
use App\Http\Controllers\web\WebController;


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
Route::get('/shipment/cart/edit/{id}', [WebController::class, 'editShipmentCartItem'])->name('shipment.cart.edit');
Route::post('/shipment/cart/update/{id}', [WebController::class, 'updateShipmentCartItem'])->name('shipment.cart.update');
Route::get('/shipment/cart/delete/{id}', [WebController::class, 'deleteShipmentCartItem'])->name('shipment.cart.delete');
Route::get('/shipment/leads', [WebController::class, 'shipmentLeads'])->middleware('auth')->name('shipment.leads');
Route::get('/track-and-trace', [WebController::class, 'trackShipment'])->name('shipment.track');
Route::get('/track-and-trace/{trackingNumber}/invoice', [WebController::class, 'downloadShipmentInvoice'])->name('shipment.invoice.download');


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

});
