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
    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array{
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function employees(){
        return $this->hasMany(Employee::class);
    }
    public function userAffiliations(){
        return $this->hasMany(UserAffiliation::class);
    }
    public function userOfferings(){
        return $this->hasMany(UserOffering::class);
    }
    public function userServices(){
        return $this->hasMany(UserService::class);
    }
    public function auditLogs(){
        return $this->hasMany(AuditLog::class);
    }
    public function projects(){
        return $this->hasMany(Project::class, 'owner_id');
    }
    public function projectWorkers(){
        return $this->hasMany(ProjectWorker::class);
    }
    public function wallets(){
        return $this->hasMany(Wallet::class);
    }
    public function professionalRecords(){
        return $this->hasMany(ProfessionalRecord::class);
    }
    public function userProfiles(){
        return $this->hasOne(UserProfile::class, 'user_id');
    }

    public function administrativeRecords(){
        return $this->hasMany(AdministrativeRecord::class);
    }
}
