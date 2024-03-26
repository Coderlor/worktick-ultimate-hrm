<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeProjectTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('employee_project', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->integer('employee_id')->index('employee_project_employee_id');
			$table->integer('project_id')->index('employee_project_project_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('employee_project');
	}

}
