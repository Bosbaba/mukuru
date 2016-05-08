<?php

/**
 * Currency API PHP Wrapper - version 0.5
 */

class CurrencyAPI extends BaseController
{
	private $__version;
	private $__base_url;
	private $__api_version;
	private $__key;
	private $__secret;
	private $__ssl;
	private $__headers;
	private $__verb;
	private $__resource;
	private $__request_body;

	public $http_status;
	public $content_type;
	public $response;
	public $error;

	/**
	 * CurrencyAPI constructor.
	 */
	public function __construct()
	{
		$this->__version 		= '0.5';
		$this->__key 			= Config::get('api.key');
		$this->__secret 		= Config::get('api.secret');
		$this->__base_url 		= Config::get('api.url');
		$this->__api_version 	= Config::get('api.version');
		$this->__ssl 			= Config::get('api.ssl');
		$this->error 			= FALSE;
	}

	/**
	 * Method that uses curl to perform the api call
	 * @param string	$url	The request path
	 * @param string 	$params	The request params
	 */
	private function __call_api($url, $params='')
	{
		$this->http_status  = NULL;
		$this->content_type = NULL;
		$this->response 		= NULL;
		$this->error 		= FALSE;

		$fields = '';

		if (($this->__verb == 'POST' || $this->__verb == 'PUT' || $this->__verb == 'DELETE') && $params != '')
		{
			$fields = (is_array($params)) ? http_build_query($params) : $params;
		}

		if ($this->__verb == 'PUT' || $this->__verb == 'POST' || $this->__verb == 'DELETE')
		{
			$this->__headers['Content-Length'] = 'Content-Length: '. strlen($fields);
		}

		$opts = array(
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_VERBOSE        => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_TIMEOUT        => 60,
			CURLOPT_USERAGENT      => 'Currency-PHP-'. $this->__version,
			CURLOPT_URL            => $url,
			CURLOPT_HTTPHEADER     => $this->__headers,
		);

		if (!$this->__ssl)
		{
			$opts[CURLOPT_SSL_VERIFYHOST] = false;
		}

		if (($this->__verb == 'POST' || $this->__verb == 'PUT' || $this->__verb == 'DELETE') && $params != '')
		{
			$opts[CURLOPT_POSTFIELDS] = $fields;
		}

		if ($this->__verb == 'POST' && is_array($params))
		{
			$opts[CURLOPT_POST] = count($params);
		}
		elseif ($this->__verb == 'PUT')
		{
			$opts[CURLOPT_CUSTOMREQUEST] = 'PUT';
		}
		elseif ($this->__verb == 'DELETE')
		{
			$opts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
		}
		elseif ($this->__verb == 'POST')
		{
			$opts[CURLOPT_POST] = TRUE;
		}

		$ch 				= curl_init();
		curl_setopt_array($ch, $opts);
		$result 			= curl_exec($ch);
		$this->http_status 	= curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$this->content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

		if ($this->http_status != 200)
		{
			// Problem with API call, we received an HTTP status code other than 200
			$this->error = TRUE;
			$error_url   = str_replace($this->__base_url, '', $url);

			if (!$this->http_status)
			{
				$this->response = (object)array('result' => 0, 'message' => 'API timed out for: '. $error_url, 'code' => APIErrors::API_TIME_OUT);
				return;
			}
			else
			{
				$this->response = (object)array('result' => 0, 'message' => 'Problem with API call: '. $url, 'code' => $this->http_status);
			}
		}

		$this->response = ($this->is_json($result) === TRUE) ? json_decode($result) : $result;

		// Nothing was returned from the API
		if ($this->response === FALSE)
		{
			$error_message = curl_error($ch);
			Log::error("CURL error: $error_message URL: {$url} ");

			$this->response = (object) array('result' => 0, 'message' => 'Problem with API call: ' . $error_message, 'code' => 409);
		}

		curl_close($ch);
	}

	/**
	 * Build the api headers
	 *
	 * @param string $format	content formate
	 * @param string $input		request data
	 */
	private function __api_headers($format='application/json', $input='')
	{
		$this->__headers 				 = array();
		$this->__headers['Date'] 		 = 'Date: '. gmdate('D, d M Y H:i:s T');
		$this->__headers['Content-Type'] = 'Content-Type: '. $format;

		// Don't specify the accept format if data is being sent as an octet-stream
		if ($format == 'application/json' || $format == 'application/xml')
		{
			$this->__headers['Accept'] 	 = 'Accept: '. $format;
		}

		$this->__request_body 			  = $input;
		$signature						  = $this->__sign_data(
												$this->__verb,
												$format,
												$this->__resource,
												$input
											);

		$this->__headers['Authorization'] = 'Authorization: ' . base64_encode($this->__key) . ':' . $signature;
	}

	/**
	 * Generate the auth string
	 *
	 * @param string 	$verb  			POST, PUT, DELETE and GET
	 * @param string	$content_type
	 * @param string	$resource		Query string
	 * @param string 	$input			JSON request data
	 * @return string
	 */
	private function __sign_data($verb, $content_type, $resource, $input = '')
	{
		$request =  $verb
					. "\n" . $content_type
					. "\n" . $resource
					. "\n" . base64_encode($this->__hash_data($input));

        return base64_encode($this->__hash_data($request));
	}

	/**
	 * Creates a HMAC-SHA1 hash
	 *
	 * @internal 	Used by __sign_data()
	 * @param 		string $data Data to hash
	 * @return 		string
	 */
	private function __hash_data($data)
	{
		return hash_hmac('sha1', $data, $this->__secret, true);
	}

	/**
	 * Check if data is valid json
	 *
	 * @param $json
	 * @return bool
	 */
	public function is_json($json)
	{
		json_decode($json);

		return (json_last_error() == JSON_ERROR_NONE);
	}

	private function __process_response($method, $line)
	{
		if (isset($this->response->result))
		{
			if ($this->response->result)
			{
				return isset($this->response->data) ? $this->response->data : TRUE;
			}
			else
			{
				Log::error(sprintf('ERROR[%s - line %s]: %s - Code: %s', $method, $line, $this->response->message, $this->response->code));
			}
		}
		else
		{
			Log::error(sprintf('ERROR[%s - line %s]: Invalid api response - Code: %s', $method, $line, APIErrors::INVALID_API_RESPONSE));
		}

		$this->error = "Oops something went wrong. Please try agian later.";

		return FALSE;
	}

	/**
	 * Get currency data to use
	 *
	 * @return currency Data
	 */
	public function get_currency()
	{
		if (Session::has('currency'))
		{
			return Session::get('currency');
		}

		$this->__resource = 'api/' . $this->__api_version . '/currency';
		$this->__verb 	  = 'GET';
		$url 			  = $this->__base_url . $this->__resource;

		$this->__api_headers();
		$this->__call_api($url);

		$currency = $this->__process_response(__METHOD__, __LINE__);

		if ($currency)
		{
			Session::set('currency', $currency);
		}

		return $currency;
	}

	/**
	 * Save the order to the database using the database
	 *
	 * @param 	string   $json 	JSON string containing the order data
	 * @return mixed
	 */
	public function order($json)
	{
		$this->__resource = 'api/' . $this->__api_version . '/order';
		$this->__verb 	  = 'POST';
		$url 			  = $this->__base_url . $this->__resource;

		$this->__api_headers('application/json', $json);
		$this->__call_api($url, $json);

		return $this->__process_response(__METHOD__, __LINE__);
	}
}