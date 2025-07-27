<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Specialization extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['profession_id', 'code', 'name', 'description'];

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
