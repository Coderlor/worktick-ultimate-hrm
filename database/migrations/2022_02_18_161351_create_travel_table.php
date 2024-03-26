<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTravelTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('travel', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->integer('id', true);
			$table->integer('company_id')->index('travels_company_id');
			$table->integer('employee_id')->index('travels_employee_id');
			$table->date('start_date');
			$table->date('end_date');
			$table->string('visit_purpose', 192);
			$table->string('visit_place', 192);
			$table->string('travel_mode', 192);
			$table->integer('arrangement_type_id')->index('travels_arrangement_type_id');
			$table->decimal('expected_budget', 65)->default(0.00);
			$table->decimal('actual_budget', 65)->default(0.00);
			$table->string('status', 191);
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
		Schema::drop('travel');
	}

}
