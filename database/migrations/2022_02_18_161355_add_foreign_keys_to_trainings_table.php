<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToTrainingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('trainings', function(Blueprint $table)
		{
			$table->foreign('company_id', 'trainings_company_id')->references('id')->on('companies')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('trainer_id', 'trainings_trainer_id')->references('id')->on('trainers')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('training_skill_id', 'trainings_training_skill_id')->references('id')->on('training_skills')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('trainings', function(Blueprint $table)
		{
			$table->dropForeign('trainings_company_id');
			$table->dropForeign('trainings_trainer_id');
			$table->dropForeign('trainings_training_skill_id');
		});
	}

}
