<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'capacity',
        'notes',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function setOccupied(): void
    {
        $this->status = 'occupied';
        $this->save();
    }

    public function setAvailable(): void
    {
        $this->status = 'available';
        $this->save();
    }

    public function setNeedsCleaning(): void
    {
        $this->status = 'needs_cleaning';
        $this->save();
    }
}
