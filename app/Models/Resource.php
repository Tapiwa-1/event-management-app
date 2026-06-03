<?php

namespace App\Models;

use Database\Factories\ResourceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resource extends Model
{
    /** @use HasFactory<ResourceFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'quantity',
        'status',
        'notes',
    ];

    protected $attributes = [
        'quantity' => 1,
        'status' => 'Available',
    ];

    /**
     * @return HasMany<ResourceBooking, $this>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(ResourceBooking::class);
    }

    public function availableQuantityFor(string $date, ?int $ignoreBookingId = null): int
    {
        $reserved = $this->bookings()
            ->whereDate('booking_date', $date)
            ->where('status', '!=', 'Cancelled')
            ->when($ignoreBookingId, fn ($query) => $query->whereKeyNot($ignoreBookingId))
            ->sum('quantity');

        return max(0, $this->quantity - (int) $reserved);
    }
}
