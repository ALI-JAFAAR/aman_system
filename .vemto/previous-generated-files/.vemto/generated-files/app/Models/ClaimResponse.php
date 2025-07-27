<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClaimResponse extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'claim_id',
        'action',
        'actor_type',
        'actor_id',
        'message',
    ];

    public function claim()
    {
        return $this->belongsTo(Claim::class);
    }
}
