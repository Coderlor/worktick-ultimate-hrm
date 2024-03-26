<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectDocument extends Model
{
    use HasFactory;


    protected $dates = ['deleted_at'];

    protected $fillable = [
        'title','description','attachment','project_id'
    ];

    protected $casts = [
        'project_id'  => 'integer',
    ];



    public function Project()
    {
        return $this->hasOne('App\Models\Project', 'id', 'project_id');
    }

}
