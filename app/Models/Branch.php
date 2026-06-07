<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'address',
        'pengelola_cabang',
        'type', // 'internal' or 'mitra'
        'opened_date',
        'notes',
        'status',
    ];

    public function reports()
    {
        return $this->hasMany(BranchReport::class);
    }
}
