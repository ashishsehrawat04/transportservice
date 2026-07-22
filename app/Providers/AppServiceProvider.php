<?php

namespace App\Providers;

use App\Models\ShipmentPayment;
use App\Models\TransportLead;
use App\Models\User;
use App\Models\WarehouseLead;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['Admin.Layout', 'admin.Layout'], function ($view) {
            $pendingLeads = TransportLead::where('admin_status', 'pending')->count();
            $pendingWarehouseLeads = WarehouseLead::where('admin_status', 'pending')->count();
            $pendingPayments = ShipmentPayment::where('status', 'pending')->count();
            $pendingUsers = User::where('role', 'user')->where('status', 'pending')->count();

            $view->with('adminHeader', [
                'pendingLeads' => $pendingLeads,
                'pendingWarehouseLeads' => $pendingWarehouseLeads,
                'pendingPayments' => $pendingPayments,
                'pendingUsers' => $pendingUsers,
                'notificationCount' => $pendingLeads + $pendingWarehouseLeads + $pendingPayments + $pendingUsers,
                'todayRevenue' => (float) ShipmentPayment::where('status', 'success')->whereDate('created_at', today())->sum('amount'),
                'recentLeads' => TransportLead::with(['user:id,name,email,mobile', 'cityRoute'])
                    ->latest()
                    ->limit(4)
                    ->get(),
                'recentPayments' => ShipmentPayment::with(['user:id,name,email,mobile', 'transportLead:id,tracking_number'])
                    ->latest()
                    ->limit(4)
                    ->get(),
                'recentUsers' => User::where('role', 'user')
                    ->latest()
                    ->limit(3)
                    ->get(),
            ]);
        });
    }
}
