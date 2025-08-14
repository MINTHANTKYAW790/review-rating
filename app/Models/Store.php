<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'phone',
        'email',
        'logo_url',
        'qr_identifier',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function booted()
    {
        static::saving(function ($store) {
            $store->slug = Str::slug($store->name);
        });
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
