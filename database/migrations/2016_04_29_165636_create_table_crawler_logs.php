<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCrawlerLogs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('crawler_logs', function (Blueprint $table) {
			$table->increments('id');
			$table->string('object_id');
			$table->tinyInteger('type');
			$table->text('previous_page')->nullable();
			$table->text('next_page')->nullable();
			$table->timestamps();

			$table->index('object_id');
			$table->unique(array('object_id', 'type'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('crawler_logs');
	}

}
