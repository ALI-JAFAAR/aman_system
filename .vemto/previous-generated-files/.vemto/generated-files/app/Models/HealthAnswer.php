<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HealthAnswer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['user_service_id', 'question_key', 'answer'];

    public function userService()
    {
        return $this->belongsTo(UserService::class);
    }
}
