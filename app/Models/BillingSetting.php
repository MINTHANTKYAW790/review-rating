<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingSetting extends Model
{
    protected $fillable = [
        'kbz_number',
        'wave_number',
        'account_name',
        'instructions',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'kbz_number' => null,
            'wave_number' => null,
            'account_name' => null,
            'instructions' => null,
        ]);
    }
}
