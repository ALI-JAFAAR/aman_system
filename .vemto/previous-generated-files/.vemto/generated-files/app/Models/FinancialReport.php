<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FinancialReport extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'report_type',
        'parameters',
        'file_path',
        'generated_at',
        'generated_by',
        'notes',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'generated_by');
    }
}
