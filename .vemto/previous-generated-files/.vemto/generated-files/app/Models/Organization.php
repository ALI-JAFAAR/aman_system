<?php

namespace App\Models;

use App\Enums\OrganizationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Organization extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'details',
        'organization_id',
        'code',
        'next_identity_sequence',
    ];

    protected function casts(): array
    {
        return [
            'type' => 'App\Enums\OrganizationType',
        ];
    }

    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function userAffiliations()
    {
        return $this->hasMany(UserAffiliation::class);
    }

    public function partnerOfferings()
    {
        return $this->hasMany(PartnerOffering::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function reconciliations()
    {
        return $this->hasMany(Reconciliation::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function organizationSpecializations()
    {
        return $this->hasMany(OrganizationSpecialization::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->useDisk('public')
            ->singleFile();

        $this->addMediaCollection('documents')->useDisk('public');
    }
}
