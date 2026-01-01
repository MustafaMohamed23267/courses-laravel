<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class courses extends Model
{
    /** @use HasFactory<\Database\Factories\CoursesFactory> */
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'image_url',
        'level',
        'videos',
        'requirements',
        'rating',
        'duration',
        'category_id',
        'instructor_id',
    ];

    /**
     * Relationships
     */


    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id')->wherein('role', ['instructor ' , 'admin']);
    } 

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
      public function users()
    {
        return $this->belongsToMany(
            User::class,
            'bookings',      // pivot table
            'course_id',     // foreign key in bookings
            'user_id'        // foreign key in bookings
        );
    }
}
