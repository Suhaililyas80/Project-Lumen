<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Useractivity extends Model
{
    protected $table = 'useractivity';

    protected $fillable = [
        'user_id',
        'email',
        'login_time',
        'logout_time',
        'duration',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}