<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryfieldTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('category_field', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('category_id')->unsigned()->nullable();
			$table->integer('field_id')->unsigned()->nullable();
			$table->boolean('disabled_in_subcategories')->unsigned()->nullable()->default('0');
			$table->integer('parent_id')->unsigned()->nullable();
			$table->integer('lft')->unsigned()->nullable();
			$table->integer('rgt')->unsigned()->nullable();
			$table->integer('depth')->unsigned()->nullable();
			$table->unique(["category_id", "field_id"]);
		});
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('category_field');
	}
}
