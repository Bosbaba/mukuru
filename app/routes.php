<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

/**
 * Mukuru Practical Test Landing Page
 */

Route::get('/', ['uses' => 'HomeController@weSellCurrency', 'as' => 'get_currency']);
Route::post('calculate', ['uses' => 'HomeController@post_calculate', 'as' => 'calculate']);
Route::post('buy', ['uses' => 'HomeController@post_buy', 'as' => 'buy']);

/**
 * API Routes with version control
 */
Route::group(['prefix' => 'api'], function()
{
	Route::group(['prefix' => 'v1'], function()
	{
		Route::post('order', ['uses' => 'APIController@post_order', 'as' => 'post_order']);
		Route::get('currency', ['uses' => 'APIController@get_currency', 'as' => 'currency']);
	});
});

Route::get('test', function()
{
	Session::forget('currency');
//	print_r(Session::all());
//	Currency::update_currency('USD', 0.8923329);
//	print_r($_SERVER); exit;
//	Mail::send('emails.mukuru', [
//		'local_amount' => '7878.88',
//		'foreign_amount' => '88989888.88',
//		'cur' => $currency
//	], function($message)
//	{
//		$message->to('jaco@zendfusion.com', 'John Smith')->subject('Mukuru purchase order success');
//	});
});

Route::get('currency_test', ['uses' => 'CurrencyAPI@get_currency', 'as' => 'cur']);

