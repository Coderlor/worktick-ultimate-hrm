<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('clients', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->integer('id', true);
			$table->string('firstname', 191);
			$table->string('lastname', 192);
			$table->string('username', 192);
			$table->bigInteger('role_users_id')->unsigned()->index('clients_role_users_id');
			$table->integer('code');
			$table->string('email', 192);
			$table->string('country', 191)->nullable();
			$table->string('city', 191)->nullable();
			$table->string('phone', 191)->nullable();
			$table->string('address', 191)->nullable();
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
		Schema::drop('clients');
	}

}
