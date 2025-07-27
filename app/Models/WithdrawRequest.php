<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WithdrawRequest extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'wallet_id',
        'amount',
        'status',
        'requested_at',
        'approved_at',
        'notes',
        'executed_at',
        'approved_by',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }
}
