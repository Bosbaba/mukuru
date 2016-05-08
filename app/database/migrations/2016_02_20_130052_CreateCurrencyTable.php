<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrencyTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('currency'))
		{
			Schema::create('currency',
				function (Blueprint $table)
				{
					$table->increments('id')->unsigned();
					$table->string('display_name', 50);
					$table->enum('currency', ['USD', 'GBP', 'KES', 'EUR']);
					$table->decimal('rate', 15, 8);
					$table->decimal('surcharge', 5, 2);
					$table->decimal('discount', 5, 2)->default(0.0);
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
		Schema::dropIfExists('currency');
	}

}
