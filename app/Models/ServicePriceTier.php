<?php

namespace App\Models;

use Database\Factories\ServicePriceTierFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePriceTier extends Model
{
    /** @use HasFactory<ServicePriceTierFactory> */
    use HasFactory;

    protected $fillable = [
        'service_id',
        'name',
        'min_guests',
        'max_guests',
        'price',
    ];

    protected $attributes = [
        'min_guests' => 1,
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function matchesGuestCount(int $guestCount): bool
    {
        return $guestCount >= $this->min_guests
            && ($this->max_guests === null || $guestCount <= $this->max_guests);
    }
}
