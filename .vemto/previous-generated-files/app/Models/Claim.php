<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Claim extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_offering_id',
        'type',
        'details',
        'accident_date',
        'amount_requested',
        'status',
        'resolution_amount',
        'resolution_note',
        'submitted_at',
    ];

    public function userOffering()
    {
        return $this->belongsTo(UserOffering::class);
    }

    public function claimResponses()
    {
        return $this->hasMany(ClaimResponse::class);
    }
}
