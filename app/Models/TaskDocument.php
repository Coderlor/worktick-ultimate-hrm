<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskDocument extends Model
{
    use HasFactory;


    protected $dates = ['deleted_at'];

    protected $fillable = [
        'title','description','attachment','task_id'
    ];

    protected $casts = [
        'task_id'  => 'integer',
    ];



    public function Task()
    {
        return $this->hasOne('App\Models\Task', 'id', 'task_id');
    }

}
