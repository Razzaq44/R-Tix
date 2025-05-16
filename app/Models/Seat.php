<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'seat_number',
    ];

    public function showtimes()
    {
        return $this->belongsToMany(Showtime::class, 'showing_seats')
                    ->withPivot('is_booked')
                    ->withTimestamps();
    }
}
