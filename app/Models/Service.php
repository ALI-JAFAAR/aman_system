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

    // DB column is `form_schema` (JSON). Keep `request_schema` as an alias.
    protected $fillable = ['code', 'name', 'description', 'form_schema', 'is_active', 'base_price'];


    protected $casts = [
        'form_schema' => 'array',
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

    public function getRequestSchemaAttribute()
    {
        return $this->form_schema;
    }

    public function setRequestSchemaAttribute($value): void{
        $this->attributes['form_schema'] = $value ? json_encode($value, JSON_UNESCAPED_UNICODE) : json_encode([], JSON_UNESCAPED_UNICODE);
    }
    public function userServices(){
        return $this->hasMany(UserService::class);
    }
}
