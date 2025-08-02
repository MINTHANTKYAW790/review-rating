<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'store_id',
        'staff_id',
        'rating_store',
        'rating_staff',
        'comment',
        'comment_staff',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
