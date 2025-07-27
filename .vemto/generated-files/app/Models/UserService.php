<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserService extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'form_data',
        'status',
        'response_data',
        'submitted_at',
        'processed_at',
        'processed_by',
        'notes',
        'user_id',
        'service_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function healthAnswers()
    {
        return $this->hasMany(HealthAnswer::class);
    }
}
