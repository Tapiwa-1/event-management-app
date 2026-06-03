<?php

namespace App\Models;

use Database\Factories\ResourceBookingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceBooking extends Model
{
    /** @use HasFactory<ResourceBookingFactory> */
    use HasFactory;

    protected $fillable = [
        'resource_id',
        'event_id',
        'booking_date',
        'quantity',
        'status',
        'notes',
    ];

    protected $attributes = [
        'quantity' => 1,
        'status' => 'Reserved',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<resource, $this>
     */
    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    /**
     * @return BelongsTo<Event, $this>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
