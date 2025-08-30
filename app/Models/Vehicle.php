<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicle extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_service_id',
        'plate_number',
        'plate_code',
        'model',
        'owner_data',
        'notes',
    ];

    public function userService(){
        return $this->belongsTo(UserService::class);
    }
}
