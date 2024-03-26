<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToTaskDiscussionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('task_discussions', function(Blueprint $table)
		{
			$table->foreign('task_id', 'task_discussions_task_id')->references('id')->on('tasks')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('user_id', 'task_discussions_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('task_discussions', function(Blueprint $table)
		{
			$table->dropForeign('task_discussions_task_id');
			$table->dropForeign('task_discussions_user_id');
		});
	}

}
