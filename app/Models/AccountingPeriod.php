<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountingPeriod extends Model{
    protected $fillable = [
        'name','start_date','end_date','is_closed','closed_by','closed_at'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_closed'  => 'boolean',
        'closed_at'  => 'datetime',
    ];
}
