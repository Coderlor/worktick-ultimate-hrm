<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $dates = ['deleted_at'];
    
    protected $fillable = [
        'title','date','time','note','company_id','department_id','status'
    ];

    protected $casts = [
        'company_id'  => 'integer',
        'department_id'  => 'integer',
    ];

    public function company()
    {
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
    }

    public function department()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }

}
