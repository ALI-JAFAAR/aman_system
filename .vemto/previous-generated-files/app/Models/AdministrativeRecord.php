<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdministrativeRecord extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'record_type',
        'title',
        'description',
        'record_date',
        'expiry_date',
        'record_data',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
