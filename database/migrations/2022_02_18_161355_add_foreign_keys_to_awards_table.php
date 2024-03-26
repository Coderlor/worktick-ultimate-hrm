<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToAwardsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('awards', function(Blueprint $table)
		{
			$table->foreign('award_type_id', 'award_award_type_id')->references('id')->on('award_types')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('company_id', 'awards_company_id')->references('id')->on('companies')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('department_id', 'awards_department_id')->references('id')->on('departments')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('employee_id', 'awards_employee_id')->references('id')->on('employees')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('awards', function(Blueprint $table)
		{
			$table->dropForeign('award_award_type_id');
			$table->dropForeign('awards_company_id');
			$table->dropForeign('awards_department_id');
			$table->dropForeign('awards_employee_id');
		});
	}

}
