<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'opening_cash',
        'closing_cash',
        'sales_total',
        'order_count',
        'cash_difference',
        'notes'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'opening_cash' => 'decimal:2',
        'closing_cash' => 'decimal:2',
        'sales_total' => 'decimal:2',
        'cash_difference' => 'decimal:2',
    ];

    /**
     * Menghubungkan shift dengan user (kasir)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan pesanan yang dibuat selama shift berlangsung
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->whereBetween('created_at', [
            $this->start_time,
            $this->end_time ?? Carbon::now()
        ]);
    }

    /**
     * Memeriksa apakah shift sudah ditutup
     */
    public function isClosed(): bool
    {
        return !is_null($this->end_time);
    }

    /**
     * Mendapatkan durasi shift dalam format yang mudah dibaca
     */
    public function getDuration(): string
    {
        $startTime = $this->start_time;
        $endTime = $this->end_time ?? Carbon::now();
        
        $diffInMinutes = $startTime->diffInMinutes($endTime);
        $hours = floor($diffInMinutes / 60);
        $minutes = $diffInMinutes % 60;
        
        return sprintf('%d jam %d menit', $hours, $minutes);
    }
    
    /**
     * Mendapatkan total pembayaran cash selama shift
     */
    public function getCashPaymentsTotal()
    {
        return Payment::whereHas('order', function($query) {
                $query->where('user_id', $this->user_id)
                      ->whereBetween('created_at', [
                          $this->start_time,
                          $this->end_time ?? Carbon::now()
                      ]);
            })
            ->where('payment_method', 'cash')
            ->sum('amount_paid');
    }
    
    /**
     * Mendapatkan total pembayaran non-cash selama shift
     */
    public function getNonCashPaymentsTotal()
    {
        return Payment::whereHas('order', function($query) {
                $query->where('user_id', $this->user_id)
                      ->whereBetween('created_at', [
                          $this->start_time,
                          $this->end_time ?? Carbon::now()
                      ]);
            })
            ->where('payment_method', '!=', 'cash')
            ->sum('amount_paid');
    }
}
