<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasRoles;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all of the employees.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get all of the userAffiliations.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userAffiliations()
    {
        return $this->hasMany(UserAffiliation::class);
    }

    /**
     * Get all of the userOfferings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userOfferings()
    {
        return $this->hasMany(UserOffering::class);
    }

    /**
     * Get all of the userServices.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userServices()
    {
        return $this->hasMany(UserService::class);
    }

    /**
     * Get all of the auditLogs.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Get all of the projects.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projects()
    {
        return $this->hasMany(Project::class, 'owner_id');
    }

    /**
     * Get all of the projectWorkers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projectWorkers()
    {
        return $this->hasMany(ProjectWorker::class);
    }

    /**
     * Get all of the wallets.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    /**
     * Get all of the professionalRecords.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function professionalRecords()
    {
        return $this->hasMany(ProfessionalRecord::class);
    }

    /**
     * Get the userProfiles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userProfiles()
    {
        return $this->hasOne(UserProfile::class, 'user_id');
    }

    /**
     * Get all of the administrativeRecords.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function administrativeRecords()
    {
        return $this->hasMany(AdministrativeRecord::class);
    }
}
