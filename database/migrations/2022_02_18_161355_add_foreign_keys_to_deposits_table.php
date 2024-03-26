<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToDepositsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('deposits', function(Blueprint $table)
		{
			$table->foreign('account_id', 'deposit_account_id')->references('id')->on('accounts')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('deposit_category_id', 'deposit_category_id')->references('id')->on('deposit_categories')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('payment_method_id', 'deposit_payment_method_id')->references('id')->on('payment_methods')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('deposits', function(Blueprint $table)
		{
			$table->dropForeign('deposit_account_id');
			$table->dropForeign('deposit_category_id');
			$table->dropForeign('deposit_payment_method_id');
		});
	}

}
