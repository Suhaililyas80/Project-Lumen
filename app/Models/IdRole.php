<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

// Uncomment if you really want JWT on Role
// use Tymon\JWTAuth\Contracts\JWTSubject;

class IdRole extends Model
{
    protected $table = 'idrole';

    // If your primary key is not 'id'
    protected $primaryKey = 'role_id';

    protected $fillable = [
         'role',
    ];
    public function users(){
        return $this->belongsToMany(User::class,'roleuser','role_id','user_id');
    }

    // Only include JWT methods if you want JWT authentication for Role model
    /*
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    */
}