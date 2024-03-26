<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAwardsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('awards', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->integer('id', true);
			$table->integer('company_id')->index('awards_company_id');
			$table->integer('department_id')->index('awards_department_id');
			$table->integer('employee_id')->index('awards_employee_id');
			$table->integer('award_type_id')->index('award_award_type_id');
			$table->date('date');
			$table->string('gift', 192);
			$table->decimal('cash', 65);
			$table->string('photo', 192)->nullable();
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
		Schema::drop('awards');
	}

}
