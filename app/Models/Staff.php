<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Staff extends Model
{
    use HasFactory;

    // Table name (optional if it follows Laravel naming convention)
    protected $table = 'staff';

    // Fillable fields for mass assignment
    protected $fillable = [
        'store_id',
        'name',
        'image_url',
        'role',
        'rating',
    ];

    // Cast attributes (optional: ensures rating is treated as float)
    protected $casts = [
        'rating' => 'float',
    ];

    /**
     * Get the store that owns the staff.
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
