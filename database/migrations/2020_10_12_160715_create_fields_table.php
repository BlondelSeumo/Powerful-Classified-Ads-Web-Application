<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('fields', function (Blueprint $table) {
			$table->increments('id');
			$table->enum('belongs_to', ['posts', 'users']);
			$table->string('translation_lang', 10)->nullable();
			$table->integer('translation_of')->unsigned()->nullable();
			$table->string('name', 100)->nullable();
			$table->string('type', 50)->default('text');
			$table->integer('max')->unsigned()->nullable()->default('255');
			$table->string('default', 255)->nullable();
			$table->boolean('required')->unsigned()->nullable();
			$table->boolean('use_as_filter')->nullable()->default('0');
			$table->string('help', 255)->nullable();
			$table->boolean('active')->unsigned()->nullable();
			$table->index(["translation_lang"]);
			$table->index(["translation_of"]);
			$table->index(["belongs_to"]);
		});
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('fields');
	}
}
