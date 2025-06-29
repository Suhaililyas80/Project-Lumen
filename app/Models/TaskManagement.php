<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;


class TaskManagement extends Model
{
    use SoftDeletes;

    protected $table = 'taskmanagement'; // Important!

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'assigned_by',
        'start_date',
        'end_date',
        'status'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}