<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAffiliation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['status', 'organization_id', 'user_id', 'joined_at','identity_number'];

    public function organization(){
        return $this->belongsTo(Organization::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function userProfessions(){
        return $this->hasMany(UserProfession::class);
    }
}
