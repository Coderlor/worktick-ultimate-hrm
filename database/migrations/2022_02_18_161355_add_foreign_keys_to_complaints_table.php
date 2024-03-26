<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToComplaintsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('complaints', function(Blueprint $table)
		{
			$table->foreign('company_id', 'complaint_company_id')->references('id')->on('companies')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('employee_against', 'complaint_employee_against')->references('id')->on('employees')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('employee_from', 'complaint_employee_from')->references('id')->on('employees')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('complaints', function(Blueprint $table)
		{
			$table->dropForeign('complaint_company_id');
			$table->dropForeign('complaint_employee_against');
			$table->dropForeign('complaint_employee_from');
		});
	}

}
