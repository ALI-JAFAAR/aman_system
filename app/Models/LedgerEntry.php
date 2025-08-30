<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LedgerEntry extends Model{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = [
        'invoice_id','reference_type','reference_id','account_code','entry_type',
        'amount','description','created_by','is_locked','posted_at',
    ];
    protected $casts = ['posted_at' => 'datetime'];

    public function employee(){
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function reconciliationEntries(){
        return $this->hasMany(ReconciliationEntry::class);
    }

    public function scopeForPartnerOrg($q, int $partnerOrgId)
    {
        // 2100 entries related to user_offerings -> partner_offerings.organization_id = partnerOrgId
        return $q->select('ledger_entries.*')
            ->join('user_offerings as uo', function ($j) {
                $j->on('ledger_entries.reference_id', '=', 'uo.id')
                    ->where('ledger_entries.reference_type', '=', \App\Models\UserOffering::class);
            })
            ->join('partner_offerings as po', 'po.id', '=', 'uo.partner_offering_id')
            ->where('po.organization_id', $partnerOrgId)
            ->where('ledger_entries.account_code', '2100')
            ->whereNull('ledger_entries.deleted_at')
            ->distinct();
    }

    public function scopeForHostOrg($q, int $hostOrgId){
        // 2200 entries tied to the same invoice as offering items for that host org
        return $q->select('ledger_entries.*')
            ->join('invoice_items as ii', 'ii.invoice_id', '=', 'ledger_entries.invoice_id')
            ->where('ii.item_type', 'offering')
            ->where('ii.organization_id', $hostOrgId)
            ->where('ledger_entries.account_code', '2200')
            ->whereNull('ledger_entries.deleted_at')
            ->distinct();
    }

    public function scopeUnreconciled($q){
        return $q->leftJoin('reconciliation_entries as re', 're.ledger_entry_id', '=', 'ledger_entries.id')
            ->whereNull('re.id');
    }

}
