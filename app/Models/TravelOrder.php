<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelOrder extends Model
{
    use HasFactory;

    protected $table = 'travel_order';

    protected $fillable = ['requester', 'destination', 'departure_date', 'return_date'];

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Status::tryFrom($value)?->name ?? 'Unknown',
            set: fn($value) => is_numeric($value) ? (int) $value : Status::fromName($value)
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester', 'id');
    }
}
