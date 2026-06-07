<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomingStock extends Model
{
    protected $fillable = [
        'raw_material_id',
        'quantity',
        'incoming_date',
        'notes',
    ];

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($incomingStock) {
            $rawMaterial = $incomingStock->rawMaterial;
            if ($rawMaterial) {
                $rawMaterial->increment('stock', $incomingStock->quantity);
            }
        });

        static::updated(function ($incomingStock) {
            if ($incomingStock->wasChanged('quantity') || $incomingStock->wasChanged('raw_material_id')) {
                $oldQty = $incomingStock->getOriginal('quantity');
                $newQty = $incomingStock->quantity;
                $oldMaterialId = $incomingStock->getOriginal('raw_material_id');
                $newMaterialId = $incomingStock->raw_material_id;

                if ($oldMaterialId != $newMaterialId) {
                    $oldMaterial = RawMaterial::find($oldMaterialId);
                    if ($oldMaterial) {
                        $oldMaterial->decrement('stock', $oldQty);
                    }
                    $newMaterial = RawMaterial::find($newMaterialId);
                    if ($newMaterial) {
                        $newMaterial->increment('stock', $newQty);
                    }
                } else {
                    $rawMaterial = $incomingStock->rawMaterial;
                    if ($rawMaterial) {
                        $diff = $newQty - $oldQty;
                        $rawMaterial->increment('stock', $diff);
                    }
                }
            }
        });
    }
}
