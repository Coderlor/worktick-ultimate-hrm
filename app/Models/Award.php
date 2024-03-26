<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Award extends Model
{
    use HasFactory;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'employee_id','company_id','department_id','award_type_id','date','gift','cash','photo','note'
    ];

    protected $casts = [
        'employee_id'  => 'integer',
        'company_id'  => 'integer',
        'department_id'  => 'integer',
        'award_type_id'=>'integer',
        'cash'         => 'double',
    ];

    public function company()
    {
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
    }

    public function department()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }

    public function employee()
    {
        return $this->belongsTo('App\Models\Employee');
    }

    public function award_type()
    {
        return $this->belongsTo('App\Models\AwardType');
    }
}
