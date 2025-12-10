<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $table = 'role_permission'; 
    protected $fillable = ['role', 'permission'];

}
