<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contract extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'organization_id',
        'service_type',
        'initiator_type',
        'platform_rate',
        'organization_rate',
        'partner_rate',
        'contract_start',
        'contract_end',
        'notes',
        'partner_offering_id',
        'platform_share',
        'organization_share',
        'partner_share',
        'contract_version',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function reconciliations()
    {
        return $this->hasMany(Reconciliation::class);
    }

    public function partnerOffering()
    {
        return $this->belongsTo(PartnerOffering::class);
    }
}
