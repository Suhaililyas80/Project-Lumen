<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Notification extends Model
{
    // use SoftDeletes;

    protected $table = 'Notificationtable'; // Important!

    protected $fillable = [
        'id',
        'type',
        'user_id',
        'data',
        'read_at'
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }
    public function user()
    {
        return $this->hasMany(User::class, 'notifiable_id', 'id');
    }
}