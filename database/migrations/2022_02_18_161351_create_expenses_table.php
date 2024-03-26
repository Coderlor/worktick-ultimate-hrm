<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('expenses', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->integer('id', true);
			$table->integer('account_id')->index('expenses_account_id');
			$table->integer('expense_category_id')->index('expenses_category_id');
			$table->decimal('amount', 10);
			$table->integer('payment_method_id')->index('expenses_payment_method_id');
			$table->date('date');
			$table->string('expense_ref', 192);
			$table->text('description')->nullable();
			$table->string('attachment', 192)->nullable();
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
		Schema::drop('expenses');
	}

}
