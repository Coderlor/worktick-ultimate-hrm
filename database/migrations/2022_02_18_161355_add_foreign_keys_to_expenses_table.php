<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToExpensesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('expenses', function(Blueprint $table)
		{
			$table->foreign('account_id', 'expenses_account_id')->references('id')->on('accounts')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('expense_category_id', 'expenses_category_id')->references('id')->on('expense_categories')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('payment_method_id', 'expenses_payment_method_id')->references('id')->on('payment_methods')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('expenses', function(Blueprint $table)
		{
			$table->dropForeign('expenses_account_id');
			$table->dropForeign('expenses_category_id');
			$table->dropForeign('expenses_payment_method_id');
		});
	}

}
