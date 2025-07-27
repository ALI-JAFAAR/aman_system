<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reconciliation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'organization_id',
        'contract_id',
        'period_start',
        'period_end',
        'total_gross_amount',
        'total_platform_share',
        'total_organization_share',
        'total_partner_share',
        'status',
        'platform_reconciled_at',
        'platform_reconciled_by',
        'partner_reconciled_by',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'platform_reconciled_by');
    }

    public function employee2()
    {
        return $this->belongsTo(Employee::class, 'partner_reconciled_by');
    }

    public function reconciliationEntries()
    {
        return $this->hasMany(ReconciliationEntry::class);
    }
}
