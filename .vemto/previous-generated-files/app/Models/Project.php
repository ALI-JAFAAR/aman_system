<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'owner_id',
        'organization_id',
        'name',
        'description',
        'location',
        'start_date',
        'end_date',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function projectWorkers()
    {
        return $this->hasMany(ProjectWorker::class);
    }
}
