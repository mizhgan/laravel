<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class PivotCapabilityNetworkTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('capability_network', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('capability_id')->unsigned()->index();
			$table->integer('network_id')->unsigned()->index();
			$table->foreign('capability_id')->references('id')->on('capabilities')->onDelete('cascade');
			$table->foreign('network_id')->references('id')->on('networks')->onDelete('cascade');
		});
	}



	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('capability_network');
	}

}
