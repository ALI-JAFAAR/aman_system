<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['code', 'name', 'description', 'request_schema','is_active','base_price'];


    protected $casts = [
        'request_schema' => 'array',
    ];

    protected static function booted(): void{
        static::creating(function (self $m) {
            if (blank($m->code)) {
                // كود مقروء من الاسم + لاحقة قصيرة لضمان الفرادة
                $slug = Str::snake(Str::ascii($m->name ?: 'service'));
                $m->code = $slug.'_'.substr(uniqid(), -4);
            }
        });
    }
    public function setRequestSchemaAttribute($value): void{
        $this->attributes['request_schema'] = $value ? json_encode($value, JSON_UNESCAPED_UNICODE) : null;
    }
    public function userServices(){
        return $this->hasMany(UserService::class);
    }
}
