<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Package extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected $fillable = ['name', 'description', 'default_behavior'];

    public function partnerOfferings()
    {
        return $this->hasMany(PartnerOffering::class);
    }
}
