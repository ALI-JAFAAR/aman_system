<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Profession extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'code'];

    protected static function booted(): void
    {
        static::creating(function (Profession $m) {
            if (blank($m->code)) {
                // حاول توليد كود من الاسم؛ ولو الاسم عربي بالكامل استخدم عشوائي كاحتياط
                $slug = Str::slug($m->name, '_');
                if ($slug === '') {
                    $slug = strtoupper(Str::random(6));
                }

                $base = strtoupper('PROF_'.$slug);
                $code = $base; $i = 1;

                while (self::where('code', $code)->exists()) {
                    $code = $base.'_'.$i++;
                }

                $m->code = $code;
            }
        });
    }
    public function specializations()
    {
        return $this->hasMany(Specialization::class);
    }

    public function userProfessions()
    {
        return $this->hasMany(UserProfession::class);
    }
}
