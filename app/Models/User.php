<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'country',
        'password',
        'slug',
        'role',
        'status',
        'login_type',
        'wallet_balance',
        'otp',
        'otp_expires_at',
        'pincode',
        'shipment_address_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function transportLeads()
    {
        return $this->hasMany(TransportLead::class);
    }

    public function cartItems()
    {
        return $this->hasMany(TransportCartItem::class);
    }

    public function transportAddresses()
    {
        return $this->hasMany(TransportAddress::class);
    }

    public function payments()
    {
        return $this->hasMany(ShipmentPayment::class);
    }

    public function shipmentAddress()
    {
        return $this->belongsTo(ShipmentAddress::class);
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }
}
