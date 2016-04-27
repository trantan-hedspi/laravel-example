<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableFacebookPages extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('facebook_pages', function (Blueprint $table) {
			$table->increments('id');
			$table->string('page_id');
			$table->text('about');
			$table->string('category',255);
			$table->tinyInteger('can_checkin')->nullable();
			$table->tinyInteger('can_post')->nullable();
			$table->text('description')->nullable();
			$table->text('emails')->nullable();
			$table->text('link');
			$table->text('name');
			$table->text('username');
			$table->timestamps();

			$table->unique('page_id');
			$table->index(array('id','page_id'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('facebook_pages');
	}

}
