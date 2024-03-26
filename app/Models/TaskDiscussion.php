<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskDiscussion extends Model
{
    use HasFactory;


    protected $dates = ['deleted_at'];

    protected $fillable = [
        'message','user_id','task_id'
    ];

    protected $casts = [
        'user_id'  => 'integer',
        'task_id'  => 'integer',
    ];

    public function User()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function Task()
    {
        return $this->hasOne('App\Models\Task', 'id', 'task_id');
    }
}
