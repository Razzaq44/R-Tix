<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Showtime extends Model
{
    use HasFactory;

    protected $fillable =[
        'movie_id',
        'show_date',
        'time',
        'type',
        'slug',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function showingSeats()
    {
        return $this->hasMany(ShowingSeats::class);
    }

    public function seats()
    {
        return $this->belongsToMany(Seat::class, 'showing_seats')
                    ->withPivot('is_booked')
                    ->withTimestamps();
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
