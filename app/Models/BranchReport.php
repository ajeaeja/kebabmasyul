<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchReport extends Model
{
    protected $fillable = [
        'branch_id',
        'report_date',
        'cash_setoran',
        'qris_setoran',
        'omset',
        'portions_sold',
        'notes',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
