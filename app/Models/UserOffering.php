<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserOffering extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'status',
        'platform_generated_number',
        'partner_filled_number',
        'applied_at',
        'activated_at',
        'rejected_at',
        'notes',
        'partner_offering_id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function partnerOffering(){
        return $this->belongsTo(PartnerOffering::class);
    }


    public function claims()
    {
        return $this->hasMany(Claim::class);
    }
}
