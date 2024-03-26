<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeTask extends Model
{
   protected $table ="employee_task";

   protected $fillable = [
    'employee_id', 'task_id',
];

protected $casts = [
    'employee_id' => 'integer',
    'task_id' => 'integer',
];

    public function assignedTasks()
    {
        return $this->hasMany('App\Models\Task', 'id', 'task_id');
    }
}
