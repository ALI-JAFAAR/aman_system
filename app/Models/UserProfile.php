<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserProfile extends Model{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'mother_name',
        'national_id',
        'date_of_birth',
        'place_of_birth',
        'phone',
        'address_province',
        'address_district',
        'address_subdistrict',
        'address_details',
        'extra_data',
        'image',
        'identity_number'
    ];
    protected $casts = [
        'date_of_birth' => 'date',
        'extra_data'    => 'array',
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
