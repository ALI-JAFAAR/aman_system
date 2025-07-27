<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'wallet_id',
        'transaction_type',
        'amount',
        'target_wallet_id',
        'status',
        'reference_type',
        'reference_id',
        'description',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function targetWallet()
    {
        return $this->belongsTo(Wallet::class, 'target_wallet_id');
    }
}
