<?php

class HomeController extends CurrencyAPI
{
	/**
	 * Holds view data
	 * @var array
	 */
	public $data = [];

	/**
	 * Validation rules for calculations
	 *
	 * @var array
	 */
	protected $calc_rules = [
		'calc_id' 		=> 'required|min:1|integer',
		'calc_amount'  	=> 'required|numeric',
	];

	/**
	 * Validation rules for purchases
	 *
	 * @var array
	 */
	protected $buy_rules = [
		'buy_token' => 'required|alpha_num|size:64',
	];

	public function __construct()
	{
		parent::__construct();
		$this->data['load_error'] = FALSE;
	}

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	/**
	 * Our graceful home page
	 *
	 * @return mixed
	 */
	public function weSellCurrency()
	{
		$currency = $this->get_currency();

		$icons = [
			'USD' => 'dollar',
			'EUR' => 'eur',
			'GBP' => 'gbp',
			'KES' => 'bank',
		];

		if ($currency)
		{
			array_walk($currency, function (&$cur) use ($icons)
			{
				$cur->icon = isset($icons[$cur->currency]) ? $icons[$cur->currency] : 'money';
			});

			$this->data['currency'] = $currency;
		}
		else
		{
			$this->data['currency'] = [];
			$this->data['load_error'];
		}

		return View::make('mukuru', $this->data);
	}

	/**
	 * Ajax call to do the currency calculations
	 * @return mixed
	 */
	public function post_calculate()
	{
		$input 		= Input::all();
		$validator 	= Validator::make($input, $this->calc_rules);
		$currency  	= Session::has('currency') ? Session::get('currency') : $this->get_currency();
		$failed 	= $validator->fails();

		if ($currency && !$failed)
		{
			$success = FALSE;

			foreach ($currency as $cur)
			{
				if ($input['calc_id'] == $cur->id)
				{
					$response['currency_id'] 	= $cur->id;
					$response['currency'] 		= $cur;
					$response['calc'] 			= Orders::calculate_rates(
																			(float) $input['calc_amount'],
                                                                            $cur->rate,
                                                                            $cur->surcharge,
                                                                            Input::has('calc_type') ? 'ZAR' : $cur->currency
                                                                        );

					$success = TRUE;
					break;
				}
			}

			if ($success)
			{
				$order_data 		= json_encode($response);
				$response['token'] 	= hash('sha256', $order_data . microtime(true));

				//Store calculations in session so it doesn't get tampered with
				Session::set($response['token'], $order_data);

				$response['result'] 	= 1;
				$response['message'] 	= 'success';
			}
		}
		else
		{
			$response['result']  = 0;
			$response['message'] = "Oops something went wrong. Please try again later..";
		}

		return Response::json($response);
	}

	/**
	 * Save the purchase order
	 * @return mixed
	 */
	public function post_buy()
	{
		$input 				 = Input::all();
		$response['result']  = 0;
		$response['message'] = "Oops something went wrong. Please try again later..";

		$validator 			 = Validator::make($input, $this->buy_rules);
		$failed 			 = $validator->fails();

		if (!$failed && Session::has($input['buy_token']) && Session::get($input['buy_token']) != 'sent')
		{
			$saved = $this->order(Session::get($input['buy_token']));

			if ($saved)
			{
				$response['save'] = $saved;
			}

			$response['result']  = 1;
			$response['message'] = 'success';

			//Set the session to sent so that it can't be sent again
			Session::set($input['buy_token'], 'sent');
			Session::forget('currency');
		}
		elseif (Session::has($input['buy_token']) && Session::get($input['buy_token']) == 'sent')
		{
			$response['message'] = 'Request all ready send. Please re-calculate if you would like to purchase more currency.';
		}

		return Response::json($response);
	}
}
