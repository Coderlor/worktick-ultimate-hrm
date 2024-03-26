<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepositsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('deposits', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->integer('id', true);
			$table->integer('account_id')->index('deposit_account_id');
			$table->integer('deposit_category_id')->index('deposit_category_id');
			$table->decimal('amount', 10);
			$table->integer('payment_method_id')->index('deposit_payment_method_id');
			$table->date('date');
			$table->string('deposit_ref', 192);
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
		Schema::drop('deposits');
	}

}
