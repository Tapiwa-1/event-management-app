<?php

namespace App\Models;

use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'description',
        'default_price',
        'unit',
        'is_active',
    ];

    protected $attributes = [
        'default_price' => 0,
        'unit' => 'each',
        'is_active' => true,
    ];

    protected function casts(): array
    {
        return [
            'default_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return HasMany<QuotationItem, $this>
     */
    public function quotationItems(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    /**
     * @return HasMany<ServicePriceTier, $this>
     */
    public function priceTiers(): HasMany
    {
        return $this->hasMany(ServicePriceTier::class);
    }

    public function priceForGuestCount(int $guestCount): float
    {
        $tier = $this->priceTiers()
            ->where('min_guests', '<=', $guestCount)
            ->where(function ($query) use ($guestCount): void {
                $query->whereNull('max_guests')
                    ->orWhere('max_guests', '>=', $guestCount);
            })
            ->orderBy('min_guests')
            ->first();

        return (float) ($tier?->price ?? $this->default_price);
    }
}
