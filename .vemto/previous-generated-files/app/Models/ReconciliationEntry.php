<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReconciliationEntry extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['reconciliation_id', 'ledger_entry_id'];

    public function reconciliation()
    {
        return $this->belongsTo(Reconciliation::class);
    }

    public function ledgerEntry()
    {
        return $this->belongsTo(LedgerEntry::class);
    }
}
