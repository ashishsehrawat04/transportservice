<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\Response;
use App\Models\CityRoute;
use App\Models\TransportLead;
use App\Models\TransportQuote;
use App\Models\TransportServicePrice;
use App\Models\ShipmentPayment;


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
                'address_line_1',
                'address_line_2',
                'city',
                'state',
                'country',
                'pincode',
                'wallet_balance',
                'slug'
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

    public function AdminGetUserDetails($id)
    {
        try {
            $user = User::with([
                'transportLeads.cityRoute',
                'payments',
                'walletTransactions',
            ])->find($id);

            if (!$user) {
                return Response::error(
                    'User not found',
                    404
                );
            }

            return Response::success(
                $user,
                'User details fetched successfully',
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
            $leads = TransportLead::with(['user:id,name,email', 'cityRoute', 'assignedUser:id,name'])
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

    public function AdminGetPayments()
    {
        try {
            $payments = ShipmentPayment::with([
                    'user:id,name,email,mobile',
                    'transportLead:id,tracking_number,item_name,city_route_id',
                    'transportLead.cityRoute',
                ])
                ->latest()
                ->get();

            if ($payments->isEmpty()) {
                return Response::error(
                    'No payments found',
                    404
                );
            }

            return Response::success(
                $payments,
                'Payments fetched successfully',
                200
            );

            } catch (\Exception $e) {
                return Response::error(
                    $e->getMessage(),
                    500
                );
            }
    }

    public function AdminGetTransportQuotes()
    {
        try {
            $quotes = TransportQuote::with(['user:id,name,email,mobile', 'transportLead:id,tracking_number'])
                ->latest()
                ->get();

            if ($quotes->isEmpty()) {
                return Response::error(
                    'No transport quotes found',
                    404
                );
            }

            return Response::success(
                $quotes,
                'Transport quotes fetched successfully',
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
