<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('settings', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->integer('id', true);
			$table->integer('currency_id')->nullable()->index('settings_currency_id');
			$table->string('email', 191);
			$table->string('CompanyName', 191);
			$table->string('CompanyPhone', 191);
			$table->string('CompanyAdress', 191);
			$table->string('footer', 191);
			$table->string('developed_by', 191);
			$table->string('logo', 191)->nullable();
			$table->string('default_language', 192)->default('en');
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
		Schema::drop('settings');
	}

}
