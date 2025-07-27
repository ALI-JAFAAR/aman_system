<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profession extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'code'];

    public function specializations()
    {
        return $this->hasMany(Specialization::class);
    }

    public function userProfessions()
    {
        return $this->hasMany(UserProfession::class);
    }
}
