<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class IdRole extends Model
{
    protected $table = 'idrole';

    // If your primary key is not 'id'
    protected $primaryKey = 'role_id';

    protected $fillable = [
        'role',
    ];
    public function users()
    {
        return $this->belongsToMany(User::class, 'roleuser', 'role_id', 'user_id');
    }
}