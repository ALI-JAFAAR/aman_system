<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['job_title', 'salary', 'user_id', 'organization_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function userServices()
    {
        return $this->hasMany(UserService::class);
    }

    public function ledgerEntries()
    {
        return $this->hasMany(LedgerEntry::class, 'created_by');
    }

    public function platformPeconciliations()
    {
        return $this->hasMany(Reconciliation::class, 'platform_reconciled_by');
    }

    public function reconciliations()
    {
        return $this->hasMany(Reconciliation::class, 'platform_reconciled_by');
    }

    public function partnerReconciliations()
    {
        return $this->hasMany(Reconciliation::class, 'platform_reconciled_by');
    }

    public function userProfessions()
    {
        return $this->hasMany(UserProfession::class, 'approved_by');
    }

    public function withdrawRequests()
    {
        return $this->hasMany(WithdrawRequest::class, 'approved_by');
    }

    public function financialReports()
    {
        return $this->hasMany(FinancialReport::class, 'generated_by');
    }
}
