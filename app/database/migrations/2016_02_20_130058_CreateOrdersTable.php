<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('orders'))
		{
			Schema::create('orders',
				function (Blueprint $table)
				{
					$table->increments('id')->unsigned();
					$table->integer('currency_id')->unsigned();
					$table->foreign('currency_id')->references('id')->on('currency');
					$table->decimal('zar_amount', 15, 2);
					$table->decimal('foreign_amount', 15, 2);
					$table->decimal('surcharge_amount', 15, 2);
					$table->timestamps();
					$table->softDeletes();
				}
			);
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('orders');
	}

}
