<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToEmployeeTrainingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('employee_training', function(Blueprint $table)
		{
			$table->foreign('employee_id', 'employee_training_employee_id')->references('id')->on('employees')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('training_id', 'employee_training_training_id')->references('id')->on('trainings')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('employee_training', function(Blueprint $table)
		{
			$table->dropForeign('employee_training_employee_id');
			$table->dropForeign('employee_training_training_id');
		});
	}

}
