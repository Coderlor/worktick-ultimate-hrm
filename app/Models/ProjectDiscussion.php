<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectDiscussion extends Model
{
    use HasFactory;


    protected $dates = ['deleted_at'];

    protected $fillable = [
        'message','user_id','project_id'
    ];

    protected $casts = [
        'user_id'  => 'integer',
        'project_id'  => 'integer',
    ];

    public function User()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function Project()
    {
        return $this->hasOne('App\Models\Project', 'id', 'project_id');
    }
}
