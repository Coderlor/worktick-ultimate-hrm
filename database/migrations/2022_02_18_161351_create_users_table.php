<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->integer('id', true);
			$table->string('username', 192);
			$table->string('email', 192);
            $table->timestamp('email_verified_at')->nullable();
			$table->string('avatar', 192)->nullable();
			$table->boolean('status')->default(1);
			$table->bigInteger('role_users_id')->unsigned()->index('users_role_users_id');
			$table->string('password', 192);
            $table->rememberToken();
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
		Schema::drop('users');
	}

}
