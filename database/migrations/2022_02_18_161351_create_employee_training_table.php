<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeTrainingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('employee_training', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->integer('employee_id')->index('employee_training_employee_id');
			$table->integer('training_id')->index('employee_training_training_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('employee_training');
	}

}
