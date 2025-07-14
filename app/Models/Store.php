<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}