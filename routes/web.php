<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\ApiController;


Route::get('/', function () {
    return view('web.index');
});

Route::GET('/admin', function () {
    return view('admin.dashboard');
})->middleware('auth')->name('admin.dashboard');

Route::GET('/admin/city-routes', function () {
    return view('admin.city-routes');
})->middleware('auth')->name('admin.city_routes');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');


// admin routes

Route::get('/admin', [AdminController::class, 'AdminDashboard'])->name('admin.dashboard');
Route::get('/admin/users', [AdminController::class, 'AdminUsers'])->name('admin.users');
Route::get('/admin/get-users', [ApiController::class, 'AdminGetUsers'])->name('adminget.users');
Route::get('/admin/users/delete/{slug}', [AdminController::class, 'AdminDeleteUsers'])->name('admin.delete.users');
Route::get('/admin/users/edit/{slug}', [AdminController::class, 'AdminEditUsers'])->name('admin.edit.users');
Route::post('/admin/users/update/{slug}', [AdminController::class, 'AdminUpdateUsers'])->name('admin.update.users');

Route::get('/admin/city-routes/manage/{id?}', [AdminController::class, 'AdminManageCityRoute'])->middleware('auth')->name('admin.manage.city_route');
Route::post('/admin/city-routes/manage/{id?}', [AdminController::class, 'AdminSaveCityRoute'])->middleware('auth')->name('admin.save.city_route');
Route::get('/admin/city-routes/delete/{id}', [AdminController::class, 'AdminDeleteCityRoute'])->middleware('auth')->name('admin.delete.city_route');
Route::get('/admin/get-city-routes', [ApiController::class, 'AdminGetCityRoutes'])->name('adminget.city.routes');

Route::get('/admin/transport-prices', [AdminController::class, 'AdminTransportPrices'])->middleware('auth')->name('admin.transport_prices');
Route::get('/admin/transport-prices/manage/{id?}', [AdminController::class, 'AdminManageTransportPrice'])->middleware('auth')->name('admin.manage.transport_price');
Route::post('/admin/transport-prices/manage/{id?}', [AdminController::class, 'AdminSaveTransportPrice'])->middleware('auth')->name('admin.save.transport_price');
Route::get('/admin/transport-prices/delete/{id}', [AdminController::class, 'AdminDeleteTransportPrice'])->middleware('auth')->name('admin.delete.transport_price');
Route::get('/admin/get-transport-prices', [ApiController::class, 'AdminGetTransportPrices'])->name('adminget.transport.prices');

Route::get('/admin/transport-leads', [AdminController::class, 'AdminTransportLeads'])->middleware('auth')->name('admin.transport_leads');
Route::get('/admin/transport-leads/manage/{id?}', [AdminController::class, 'AdminManageTransportLead'])->middleware('auth')->name('admin.manage.transport_lead');
Route::post('/admin/transport-leads/manage/{id?}', [AdminController::class, 'AdminSaveTransportLead'])->middleware('auth')->name('admin.save.transport_lead');
Route::get('/admin/transport-leads/delete/{id}', [AdminController::class, 'AdminDeleteTransportLead'])->middleware('auth')->name('admin.delete.transport_lead');
Route::get('/admin/get-transport-leads', [ApiController::class, 'AdminGetTransportLeads'])->name('adminget.transport.leads');

