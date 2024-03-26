<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeTraining extends Model
{
   protected $table ="employee_training";

   protected $fillable = [
    'employee_id', 'training_id',
];

protected $casts = [
    'employee_id' => 'integer',
    'training_id' => 'integer',
];

    public function assignedTrainings()
    {
        return $this->hasMany('App\Models\Training', 'id', 'training_id');
    }
}
