<?php

namespace App\Models;

use Database\Factories\PackageTemplateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PackageTemplate extends Model
{
    /** @use HasFactory<PackageTemplateFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'event_type',
        'description',
        'total_amount',
        'is_active',
    ];

    protected $attributes = [
        'total_amount' => 0,
        'is_active' => true,
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return HasMany<PackageTemplateItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(PackageTemplateItem::class);
    }

    public function recalculateTotal(): void
    {
        $this->forceFill([
            'total_amount' => $this->items()->sum('line_total'),
        ])->save();
    }
}
