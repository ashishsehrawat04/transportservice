<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\Response;
use App\Models\CityRoute;
use App\Models\TransportLead;
use App\Models\TransportServicePrice;


class ApiController extends Controller
{
    // Using the Response helper class for API responses

    public function AdminGetUsers()
    {
        try {

            $users = User::select(
                'id',
                'name',
                'email',
                'mobile',
                'wallet_balance', 'slug'
            )->get();

            if ($users->isEmpty()) {
                return Response::error(
                    'No users found',
                    404
                );
            }
            return Response::success(
                $users,
                'Users fetched successfully',
                200
            );

            } catch (\Exception $e) {

                return Response::error(
                    $e->getMessage(),
                    500
                );
            }
    }

    public function AdminGetCityRoutes()
    {
        try {

            $cityRoutes = CityRoute::get();


            if ($cityRoutes->isEmpty()) {
                return Response::error(
                    'No city routes found',
                    404
                );
            }
            return Response::success(
                $cityRoutes,
                'City routes fetched successfully',
                200
            );

            } catch (\Exception $e) {

                return Response::error(
                    $e->getMessage(),
                    500
                );
            }
    }

    public function AdminGetTransportPrices()
    {
        try {
            $prices = TransportServicePrice::get();

            if ($prices->isEmpty()) {
                return Response::error(
                    'No transport prices found',
                    404
                );
            }

            return Response::success(
                $prices,
                'Transport prices fetched successfully',
                200
            );

            } catch (\Exception $e) {
                return Response::error(
                    $e->getMessage(),
                    500
                );
            }
    }

    public function AdminGetTransportLeads()
    {
        try {
            $leads = TransportLead::with(['user:id,name,email', 'fromCity:id,name', 'toCity:id,name', 'assignedUser:id,name'])
                ->latest()
                ->get();

            if ($leads->isEmpty()) {
                return Response::error(
                    'No transport leads found',
                    404
                );
            }

            return Response::success(
                $leads,
                'Transport leads fetched successfully',
                200
            );

            } catch (\Exception $e) {
                return Response::error(
                    $e->getMessage(),
                    500
                );
            }
    }
}
