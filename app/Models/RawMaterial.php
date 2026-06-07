<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    protected $fillable = [
        'sku',
        'name',
        'stock',
        'unit',
        'safety_stock',
        'price',
        'status',
    ];

    public function incomingStocks()
    {
        return $this->hasMany(IncomingStock::class);
    }

    public function isBelowSafetyStock(): bool
    {
        return $this->stock <= $this->safety_stock;
    }
}
