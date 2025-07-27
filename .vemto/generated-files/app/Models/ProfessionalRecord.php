<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProfessionalRecord extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'organization',
        'start_date',
        'end_date',
        'details',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
