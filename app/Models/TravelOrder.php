<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelOrder extends Model
{
    use HasFactory;

    protected $table = 'travel_order';

    protected $fillable = ['requester', 'destination', 'departure_date', 'return_date'];

    public const STATUS = [
        0 => 'requested',
        1 => 'approved',
        2 => 'cancelled',
    ];

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => self::STATUS[$value] ?? 'Unknown',
        );
    }
}
