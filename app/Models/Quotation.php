<?php

namespace App\Models;

use Database\Factories\QuotationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    /** @use HasFactory<QuotationFactory> */
    use HasFactory;

    protected $fillable = [
        'event_id',
        'quotation_number',
        'issued_at',
        'valid_until',
        'status',
        'total_amount',
        'terms',
        'confirmed_at',
    ];

    protected $attributes = [
        'status' => 'Draft',
        'total_amount' => 0,
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'valid_until' => 'date',
            'confirmed_at' => 'datetime',
            'total_amount' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<Event, $this>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * @return HasMany<QuotationItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    /**
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function recalculateTotal(): void
    {
        $this->forceFill([
            'total_amount' => $this->items()->sum('line_total'),
        ])->save();
    }

    public function paidAmount(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function balanceDue(): float
    {
        return max(0, (float) $this->total_amount - $this->paidAmount());
    }

    public function paymentStatus(): string
    {
        $paidAmount = $this->paidAmount();

        if ($paidAmount <= 0) {
            return 'Outstanding';
        }

        if ($paidAmount < (float) $this->total_amount) {
            return 'Partial';
        }

        return 'Paid';
    }
}
