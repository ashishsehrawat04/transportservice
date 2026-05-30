<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportAuthSetting extends Model
{
    protected $fillable = [
        'email_login_enabled',
        'mobile_login_enabled',
        'google_login_enabled',
        'admin_approval_required',
    ];

    protected $casts = [
        'email_login_enabled' => 'boolean',
        'mobile_login_enabled' => 'boolean',
        'google_login_enabled' => 'boolean',
        'admin_approval_required' => 'boolean',
    ];

    public static function current(): self
    {
        return self::firstOrCreate([]);
    }
}
