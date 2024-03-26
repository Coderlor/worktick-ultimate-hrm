<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('projects', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->integer('id', true);
			$table->string('title');
			$table->integer('client_id')->index('projects_client_id');
			$table->string('priority');
			$table->date('start_date');
			$table->date('end_date');
			$table->text('summary');
			$table->text('description')->nullable();
			$table->integer('company_id')->index('projects_company_id');
			$table->string('project_progress');
			$table->string('status');
			$table->text('project_note')->nullable();
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
		Schema::drop('projects');
	}

}
