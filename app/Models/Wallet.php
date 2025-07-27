<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wallet extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'balance',
        'currency',
        'walletable_type',
        'walletable_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'wallet_id');
    }

    public function targetWallet()
    {
        return $this->hasMany(Transaction::class, 'target_wallet_id');
    }

    public function withdrawRequests()
    {
        return $this->hasMany(WithdrawRequest::class);
    }
}
