<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrganizationSpecialization extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['organization_id', 'specialization_id'];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }
}
