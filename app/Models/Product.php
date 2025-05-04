<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'price',
        'category_id',
        'is_active',
        'image_path',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(ProductIngredient::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function hasEnoughStock(): bool
    {
        foreach ($this->ingredients as $ingredient) {
            $inventoryItem = $ingredient->inventoryItem;
            if ($inventoryItem->stock_quantity < $ingredient->quantity) {
                return false;
            }
        }
        return true;
    }

    public function reduceStock(int $quantity = 1): void
    {
        foreach ($this->ingredients as $ingredient) {
            $inventoryItem = $ingredient->inventoryItem;
            $inventoryItem->stock_quantity -= ($ingredient->quantity * $quantity);
            $inventoryItem->save();
        }
    }
}
