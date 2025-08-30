<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['code', 'name', 'description', 'request_schema','is_active','base_price'];


    protected $casts = [
        'request_schema' => 'array',
    ];

    public function setRequestSchemaAttribute($value): void{
        $this->attributes['request_schema'] = $value ? json_encode($value, JSON_UNESCAPED_UNICODE) : null;
    }
    public function userServices(){
        return $this->hasMany(UserService::class);
    }
}
