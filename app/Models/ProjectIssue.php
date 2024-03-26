<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectIssue extends Model
{
    use HasFactory;


    protected $dates = ['deleted_at'];

    protected $fillable = [
        'title','comment','label','attachment','project_id','status'
    ];

    protected $casts = [
        'project_id'  => 'integer',
    ];



    public function Project()
    {
        return $this->hasOne('App\Models\Project', 'id', 'project_id');
    }

}
