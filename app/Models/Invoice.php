<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\BelongsToClinic;

class Invoice extends Model
{
    use BelongsToClinic, HasFactory, SoftDeletes;

    public const STATUS_UNPAID = 'unpaid';
    public const STATUS_PAID = 'paid';
    public const STATUS_PARTIALLY_PAID = 'partially_paid';
    public const STATUS_REFUNDED = 'refunded';

    public const ALL_STATUSES = [
        self::STATUS_UNPAID,
        self::STATUS_PAID,
        self::STATUS_PARTIALLY_PAID,
        self::STATUS_REFUNDED,
    ];

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'subtotal',
        'discount',
        'discount_reason',
        'total_amount',
        'doctor_share',
        'clinic_share',
        'payment_status',
        'is_locked',
        'refund_reason',
        'refunded_at',
        'refunded_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'doctor_share' => 'decimal:2',
            'clinic_share' => 'decimal:2',
            'is_locked' => 'boolean',
            'refunded_at' => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function refundedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'refunded_by_user_id');
    }

    /**
     * Check if invoice is locked (read-only).
     * An invoice is read-only if explicitly locked or if payment status is 'paid'.
     */
    public function isReadOnly(): bool
    {
        return $this->is_locked || $this->payment_status === self::STATUS_PAID;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::STATUS_PAID;
    }

    public function isRefunded(): bool
    {
        return $this->payment_status === self::STATUS_REFUNDED;
    }

    /**
     * Recalculate subtotal and total_amount from items and discount.
     */
    public function recalculateTotals(): void
    {
        $subtotal = $this->items()->sum('total_price');
        $this->subtotal = $subtotal;
        $discount = min($this->discount, $subtotal);
        $this->discount = $discount;
        $this->total_amount = max(0, $subtotal - $discount);
        $this->save();
    }
}
