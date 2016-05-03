<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableFbPagePosts extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('fb_page_posts', function (Blueprint $table) {
			$table->increments('id');
			$table->string('object_id');
			$table->bigInteger('fb_page_id');
			$table->longText('message')->nullable();
			$table->longText('story')->nullable();
			$table->string('created_time');
			$table->timestamps();

			$table->unique('object_id');
			$table->index('fb_page_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('fb_page_posts');
	}

}
