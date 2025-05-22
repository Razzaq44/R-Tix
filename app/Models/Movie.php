<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'url_image',
    ];

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }
}
