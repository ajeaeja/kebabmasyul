<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerOrderItem extends Model
{
    protected $fillable = [
        'partner_order_id',
        'raw_material_id',
        'quantity',
        'price',
    ];

    public function order()
    {
        return $this->belongsTo(PartnerOrder::class, 'partner_order_id');
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
