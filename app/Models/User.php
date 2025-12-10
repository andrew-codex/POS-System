<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\RolePermission;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'status',
        'email',
        'password',
        'phone',
        'role',
    ];

    public function hasPermission($permission)
    {
  
    return RolePermission::where('role', $this->role)
        ->where('permission', $permission)
        ->exists();
    }

    public function setRoleAttribute($value)
    {
        $this->attributes['role'] = strtolower(trim($value));
    }

    public function isAdmin() {
        return $this->role === 'admin';
    }

    public function isCashier() {
        return $this->role === 'cashier';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
}