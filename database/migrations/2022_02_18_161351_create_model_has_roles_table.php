<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModelHasRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('model_has_roles', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->bigInteger('role_id')->unsigned();
			$table->string('model_type');
			$table->bigInteger('model_id')->unsigned();
			$table->index(['model_id','model_type']);
			$table->primary(['role_id','model_id','model_type']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('model_has_roles');
	}

}
