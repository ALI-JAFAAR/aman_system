<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OfferingDistribution extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'partner_offering_id',
        'organization_id',
        'partner_percent',
        'platform_percent',
        'host_org_percent',
        'status',
    ];

    public function organization(){
        return $this->belongsTo(Organization::class);
    }

    public function partnerOffering(){
        return $this->belongsTo(PartnerOffering::class);
    }
}
