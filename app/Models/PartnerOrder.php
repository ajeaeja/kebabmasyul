<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerOrder extends Model
{
    protected $fillable = [
        'partner_id',
        'order_date',
        'shipping_date',
        'expedition_info',
        'shipping_cost',
        'status', // 'menunggu_dipacking', 'dipacking', 'dikirim', 'selesai'
        'payment_status', // 'lunas', 'belum_lunas'
        'payment_method', // 'transfer', 'qris', 'cash'
        'total_price',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function items()
    {
        return $this->hasMany(PartnerOrderItem::class);
    }
}
