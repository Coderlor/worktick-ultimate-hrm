<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('trainings', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->integer('id', true);
			$table->integer('trainer_id')->index('trainings_trainer_id');
			$table->integer('company_id')->index('trainings_company_id');
			$table->integer('training_skill_id')->index('trainings_training_skill_id');
			$table->string('training_cost', 192)->nullable();
			$table->date('start_date');
			$table->date('end_date');
			$table->boolean('status')->default(1);
			$table->text('description')->nullable();
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
		Schema::drop('trainings');
	}

}
