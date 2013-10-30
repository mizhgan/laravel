<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class PivotNetworkTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('network_type', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('network_id')->unsigned()->index();
			$table->integer('type_id')->unsigned()->index();
			$table->foreign('network_id')->references('id')->on('networks')->onDelete('cascade');
			$table->foreign('type_id')->references('id')->on('types')->onDelete('cascade');
		});
	}



	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('network_type');
	}

}
