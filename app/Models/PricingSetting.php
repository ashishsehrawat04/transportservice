<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingSetting extends Model
{
    protected $fillable = [
        'gst_percent',
    ];

    public static function current(): self
    {
        return self::firstOrCreate([]);
    }

    public static function gstPercent(): float
    {
        return (float) self::current()->gst_percent;
    }
}
