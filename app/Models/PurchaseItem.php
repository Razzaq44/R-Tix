<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id',
        'showing_seat_id',
        'price',
        'seat_number',
        'seat_type',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function showingSeat()
    {
        return $this->belongsTo(ShowingSeats::class);
    }
}
