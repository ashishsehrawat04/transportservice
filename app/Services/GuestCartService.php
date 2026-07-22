<?php

namespace App\Services;

use App\Models\TransportCartItem;
use App\Models\WarehouseCartItem;
use Illuminate\Support\Str;

class GuestCartService
{
    public function guestId(): string
    {
        if (!session()->has('guest_cart_id')) {
            session(['guest_cart_id' => (string) Str::uuid()]);
        }

        return session('guest_cart_id');
    }

    public function mergeToUser(int $userId): void
    {
        if (!session()->has('guest_cart_id')) {
            return;
        }

        TransportCartItem::where('guest_id', session('guest_cart_id'))
            ->update([
                'user_id' => $userId,
                'guest_id' => null,
            ]);

        WarehouseCartItem::where('guest_id', session('guest_cart_id'))
            ->update([
                'user_id' => $userId,
                'guest_id' => null,
            ]);
    }
}
