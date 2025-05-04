<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_method',
        'amount_paid',
        'change_amount',
        'transaction_id',
        'receipt_number',
        'user_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            $payment->receipt_number = 'RCPT-' . date('Ymd') . '-' . sprintf('%04d', (self::whereDate('created_at', today())->count() + 1));
            
            // Calculate change if not already set
            if ($payment->amount_paid > $payment->order->total_amount) {
                $payment->change_amount = $payment->amount_paid - $payment->order->total_amount;
            }
            
            // If payment completes, update order status
            if ($payment->status === 'completed') {
                $payment->order->markAsCompleted();
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
