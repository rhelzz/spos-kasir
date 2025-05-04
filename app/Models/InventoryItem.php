<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit',
        'stock_quantity',
        'alert_threshold',
        'cost_per_unit',
        'expiry_date',
        'notes',
    ];

    protected $casts = [
        'stock_quantity' => 'decimal:2',
        'alert_threshold' => 'decimal:2',
        'cost_per_unit' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    public function productIngredients(): HasMany
    {
        return $this->hasMany(ProductIngredient::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->alert_threshold;
    }
}
