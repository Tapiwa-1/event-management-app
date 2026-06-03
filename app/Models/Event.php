<?php

namespace App\Models;

use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'type',
        'event_date',
        'venue',
        'guest_count',
        'status',
        'special_requirements',
    ];

    protected $attributes = [
        'status' => 'Inquiry',
        'guest_count' => 0,
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Client, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return HasMany<Quotation, $this>
     */
    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    /**
     * @return HasMany<Expense, $this>
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * @return HasMany<ResourceBooking, $this>
     */
    public function resourceBookings(): HasMany
    {
        return $this->hasMany(ResourceBooking::class);
    }

    public function quotedAmount(): float
    {
        return (float) $this->quotations()->whereIn('status', ['Sent', 'Accepted'])->sum('total_amount');
    }

    public function expenseTotal(): float
    {
        return (float) $this->expenses()->sum('amount');
    }

    public function profit(): float
    {
        return $this->quotedAmount() - $this->expenseTotal();
    }
}
