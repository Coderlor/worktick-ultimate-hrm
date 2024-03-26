<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToTravelTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('travel', function(Blueprint $table)
		{
			$table->foreign('arrangement_type_id', 'travels_arrangement_type_id')->references('id')->on('arrangement_types')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('company_id', 'travels_company_id')->references('id')->on('companies')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('employee_id', 'travels_employee_id')->references('id')->on('employees')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('travel', function(Blueprint $table)
		{
			$table->dropForeign('travels_arrangement_type_id');
			$table->dropForeign('travels_company_id');
			$table->dropForeign('travels_employee_id');
		});
	}

}
