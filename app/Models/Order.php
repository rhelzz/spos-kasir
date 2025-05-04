<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Table;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'order_type',
        'table_id',
        'user_id',
        'status',
        'subtotal',
        'tax_amount',
        'tax_id',
        'discount_amount',
        'discount_id',
        'service_charge',
        'total_amount',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_number = 'ORD-' . date('Ymd') . '-' . sprintf('%04d', (self::whereDate('created_at', today())->count() + 1));
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        if ($this->tax_id) {
            $this->tax_amount = $this->tax->calculateTax($this->subtotal);
        }

        if ($this->discount_id && $this->discount->isValid()) {
            $this->discount_amount = $this->discount->calculateDiscount($this->subtotal);
        }

        $this->total_amount = $this->subtotal + $this->tax_amount + $this->service_charge - $this->discount_amount;
        $this->save();
    }

    public function markAsCompleted(): void
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();

        // Check if $this->table is actually a Table model object before calling the method
        if ($this->order_type === 'dine_in' && $this->table instanceof Table) {
            $this->table->setNeedsCleaning();
        }
    }
}
