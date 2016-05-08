<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateCurrency extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'currency:update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'This command updates the current exchange rates in the db';

	/**
	 * Create a new command instance.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$key 				= Config::get('api.external_api_key');
		$url 				= Config::get('api.external_api');
		$path 				= Config::get('api.external_api_path');
		$decimals 			= Config::get('api.external_api_max_decimals');
		$foreign_currencies = Config::get('api.external_foreign_currencies');

		//Build the query string
		$query_string = "?";
		$query_string .= 'api_key=' . $key;
		$query_string .= '&decimal_places=' . $decimals;
		$query_string .= '&date=' . gmdate('Y-m-d');
		$query_string .= '&quote=' . implode('&quote=', $foreign_currencies);

		$api_url = $url . $path . $query_string;

		try
		{
			$this->info('Request url:' . $api_url);
			$json_response = file_get_contents($api_url);
		}
		catch (Exception $e)
		{
			return $this->error(sprintf('API call failed: %s', $e->getMessage()));
		}

		try
		{
			$response = json_decode($json_response);

			if (isset($response->quotes))
			{
				foreach ($response->quotes as $currency => $rates)
				{
					$this->info(sprintf('Updating %s to %f', $currency, $rates->ask));

					//TODO:: validate input is a float
					$updated = Currency::update_currency($currency, $rates->ask);

					if ($updated)
					{
						$this->info('Success');
					}
					else
					{
						$this->error(sprintf('Currency %s does not exist in the db', $currency));
					}
				}
			}
		}
		catch (Exception $ex)
		{
			$this->error(sprintf('Currency Update failed: %s', $ex->getMessage()));
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [];
	}

}
