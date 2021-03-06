<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('items', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('description')->nullable();
			$table->double('purchase_price')->default(0);;
			$table->double('selling_price')->default(0);
			$table->string('sku')->nullable();
			$table->string('category')->nullable();
			$table->string('duration')->nullable();
			$table->string('tag_id')->nullable();
			$table->integer('reorder_level')->nullable();
			$table->integer('location_id')->nullable();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('items');
	}

}
