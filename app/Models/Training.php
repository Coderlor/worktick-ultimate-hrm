<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'trainer_id','company_id','training_skill_id','start_date','end_date',
        'training_cost','status','description'
    ];

    protected $casts = [
        'trainer_id'         => 'integer',
        'training_skill_id'  => 'integer',
        'company_id'         => 'integer',
    ];


    public function company()
    {
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
    }
    
    public function trainer()
    {
        return $this->belongsTo('App\Models\Trainer');
    }

    public function TrainingSkill()
    {
        return $this->belongsTo('App\Models\TrainingSkill');
    }

    public function assignedEmployees()
    {
        return $this->belongsToMany('App\Models\Employee');
    }
}
