<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $dates = ['deleted_at'];
    
    protected $fillable = [
        'title','time','date','description','company_id','employee_from','employee_against','reason'
    ];

    protected $casts = [
        'company_id'  => 'integer',
        'employee_from'  => 'integer',
        'employee_against'  => 'integer',
    ];

    public function company()
    {
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
    }

    public function EmployeeFrom()
    {
        return $this->hasOne('App\Models\Employee', 'id', 'employee_from');
    }

    public function EmployeeAgainst()
    {
        return $this->hasOne('App\Models\Employee', 'id', 'employee_against');
    }

}
