<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tasks', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->integer('id', true);
			$table->string('title', 192);
			$table->integer('project_id')->index('Tasks_project_id');
			$table->integer('company_id')->index('Tasks_company_id');
			$table->date('start_date');
			$table->date('end_date');
			$table->string('estimated_hour', 192)->nullable();
			$table->string('task_progress', 192)->nullable();
			$table->string('summary');
			$table->text('description')->nullable();
			$table->string('status', 192);
			$table->string('priority', 191);
			$table->text('note')->nullable();
			$table->timestamps(6);
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tasks');
	}

}
