<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courses extends Model
{
    /** @use HasFactory<\Database\Factories\CoursesFactory> */
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'image_url',
        'price',
        'level',
        'total_seats',
        'available_seats',
        'rating',
        'duration',

        'category_id'
    ];

    /**
     * Relationships
     */


    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function booking()
    {
        return $this->hasMany(Booking::class);
    }
}
