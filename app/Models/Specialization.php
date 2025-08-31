<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Specialization extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['profession_id', 'code', 'name', 'description'];

    protected static function booted(): void
    {
        static::creating(function (Specialization $m) {
            if (blank($m->code)) {
                // اجلب كود المهنة ليكون Prefix إن وُجد
                $profCode = optional(Profession::find($m->profession_id))->code ?: 'SPEC';

                $slug = Str::slug($m->name, '_');
                if ($slug === '') {
                    $slug = strtoupper(Str::random(6));
                }

                $base = strtoupper($profCode.'_'.$slug);
                $code = $base; $i = 1;

                while (self::where('code', $code)->exists()) {
                    $code = $base.'_'.$i++;
                }

                $m->code = $code;
            }
        });
    }
    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    public function organizationSpecializations()
    {
        return $this->hasMany(OrganizationSpecialization::class);
    }

    public function userProfessions()
    {
        return $this->hasMany(UserProfession::class);
    }
}
