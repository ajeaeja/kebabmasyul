<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = [
        'name',
        'owner_name',
        'phone',
        'address',
        'jenis_paket',
        'join_date',
        'mou_end_date',
        'mou_path',
        'notes',
        'status',
    ];

    public function orders()
    {
        return $this->hasMany(PartnerOrder::class);
    }
}
