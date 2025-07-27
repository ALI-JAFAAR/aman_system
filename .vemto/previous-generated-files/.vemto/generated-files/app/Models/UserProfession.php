<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserProfession extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_affiliation_id',
        'profession_id',
        'specialization_id',
        'status',
        'applied_at',
        'approved_at',
        'approved_by',
        'notes',
    ];

    public function userAffiliation()
    {
        return $this->belongsTo(UserAffiliation::class);
    }

    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }
}
