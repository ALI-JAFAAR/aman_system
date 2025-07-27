<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BankAccount extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'owner_id',
        'owner_type',
        'bank_name',
        'branch_name',
        'account_name',
        'account_number',
        'iban',
        'currency',
        'is_primary',
    ];
}
