<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PartnerOffering extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'organization_id',
        'package_id',
        'price',
        'contract_start',
        'contract_end',
        'auto_approve',
        'partner_must_fill_number',
    ];

//    protected $casts = ['contract_end' => 'datetime'];
    public function organization(){
        return $this->belongsTo(Organization::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function userOfferings()
    {
        return $this->hasMany(UserOffering::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
}
