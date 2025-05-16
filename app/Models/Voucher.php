<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_amount',
        'valid_until',
        'discount_type',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function isValid()
    {
        return $this->valid_until >= now();
    }
}
