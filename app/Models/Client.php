<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $dates = ['deleted_at'];

    protected $fillable = [
       'id','role_users_id','username','firstname','lastname','password','code','email','country','city','phone','address'
    ];

    protected $casts = [
        'id' => 'integer',
        'code' => 'integer',
        'role_users_id' => 'integer',
    ];

    public function RoleUser()
	{
        return $this->hasone('Spatie\Permission\Models\Role','id',"role_users_id");
    }

    public function projects()
    {
        return $this->hasMany('App\Models\Project');

    }
}
