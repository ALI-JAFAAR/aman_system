<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LedgerEntry extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'reference_type',
        'reference_id',
        'account_code',
        'entry_type',
        'amount',
        'description',
        'created_by',
        'is_locked',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function reconciliationEntries()
    {
        return $this->hasMany(ReconciliationEntry::class);
    }
}
