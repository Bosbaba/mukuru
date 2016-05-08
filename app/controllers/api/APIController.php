<?php

/**
 * Class APIController
 */
class APIController extends Controller
{
    /**
     * Validation rules for orders
     *
     * @var array
     */
    protected $order_rules = [
        'currency_id' => 'required|min:1|integer',
        'zar_amount'  => 'required|numeric',
    ];

    protected $http_status;
    protected $message;
    protected $error;
    protected $json_body;

    private $__key;
    private $__secret;
    private $__auth_request;
    private $__signed_request;

    /**
     * APIController constructor.
     */
    public function __construct()
    {
        $this->__key    = Config::get('api.key');
        $this->__secret = Config::get('api.secret');
        $this->reset_status();

        if (Request::format() == 'json')
        {
            Log::debug("is json request");
            $this->json_body = file_get_contents('php://input');
        }
    }

    /**
     * Reset default response data
     */
    protected function reset_status()
    {
        $this->http_status = 500;
        $this->message     = 'Oops something went wrong... Please try again later, we\'re aware of the issue.';
        $this->error       = FALSE;
    }

    /**
     * Formulate data to be return to a uniformed json response
     *
     * @param mixed $data   Optional data to returned
     * @return mixed
     */
    protected function formulate_response($data = NULL)
    {
        $return = [
            'result'  => 1,
            'message' => $this->message
        ];

        if ($this->error)
        {
            $return['result'] = 0;
            $return['code']   = $this->error;
        }
        elseif (!is_null($data))
        {
            $return['data'] = $data;
        }

        $response = Response::json($return, $this->http_status);
        $response->header('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Authenticate the signed request to see if data was tampered with
     *
     * @return bool
     */
    private function __authenticate()
    {
        $authenticated      = FALSE;

        if (Request::server('HTTP_AUTHORIZATION'))
        {
            Log::debug("Auth-header: " . Request::server('HTTP_AUTHORIZATION'));

            $auth = explode(':', Request::server('HTTP_AUTHORIZATION'));

            if ($auth && is_array($auth) && count($auth) == 2)
            {
                $key = base64_decode($auth[0]);

                if ($this->__key === $key)
                {
                    if (isset($auth[1]))
                    {
                        $this->__auth_request   = $this->__get_auth_request();
                        $this->__signed_request = $auth[1];

                        Log::debug(sprintf('Auth: %s = %s', $this->__auth_request, $this->__signed_request));
                        $authenticated = $this->__auth_request === $this->__signed_request ? TRUE : FALSE;
                    }
                }
            }
        }

        if ($authenticated !== FALSE)
        {
            return TRUE;
        }
        else
        {
            $this->http_status  = 401; //Unauthorized
            $this->message      = 'Access denied';
            $this->error        = APIErrors::ACCESS_DENIED;

            return FALSE;
        }
    }

    /**
     * Build an auth request to verify against the signed request
     *
     * @return string
     */
    private function __get_auth_request()
    {
		if (Request::isMethod('get'))
		{
			$content_type = 'application/json';
		}
		elseif (Request::server('CONTENT_TYPE'))
		{
            $content_type = Request::server('CONTENT_TYPE');
		}
        else
        {
            return '';
        }

        $method     = Request::server('REQUEST_METHOD');
        $this->uri  = Request::server('PATH_INFO');

        if (Request::server('QUERY_STRING'))
        {
            $this->uri .= '?' . Request::server('QUERY_STRING');
        }

        switch ($method)
        {
            case "GET":
            case "DELETE":
                $body = '';
            break;
            case "PUT":
            case "POST":
                $body = $this->json_body;
            break;
            default:
                $body = '';
        }

        return $this->__sign_data(
            $method,
            $content_type,
            $this->uri,
            $body
        );
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
     * Returns a json object with the currency rates
     *
     * @return mixed
     */
    public function get_currency()
    {
        $data = NULL;

        try
        {
            if ($this->__authenticate())
            {
                $this->http_status  = 200;
                $this->message      = 'Currency retrieved successfully.';
                $data               = Currency::all(['id', 'display_name', 'currency', 'rate', 'surcharge'])->toArray();
            }
        }
        catch (Exception $ex)
        {
            // TODO:: System admin should be notified that an un expected exception was thrown
            $this->reset_status();
            $this->error = APIErrors::SYSTEM_ERROR;
        }

        return $this->formulate_response($data);
    }

    /**
     * Save a purchase order and return success results
     *
     * @throws Exception Error exceptions
     * @return mixed
     */
    public function post_order()
    {
        $response = [];
        //TODO:: input validation
        try
        {
            if ($this->__authenticate())
            {
                $input          = Input::json()->all();
                $currency_data  = Currency::find($input['currency_id']);

                if (!$currency_data)
                {
                    throw new \Exception ('Invalid data supplied.', APIErrors::INVALID_API_PARAMS);
                }

                if ($currency_data->discount)
                {
                    //Apply discount
                    $old_local =  $input['calc']['local'];
                    $input['calc']['local'] = $input['calc']['local'] - (($input['calc']['local'] * $currency_data->discount) / 100);
                    //Uncomment the following line if you would like the discount subtracted from the surcharge amount
                    //$input['calc']['surcharge'] = $input['calc']['surcharge'] - ($old_local - $input['calc']['local'])
                    $response['discount']   = floatval($currency_data->discount);
                }

                Orders::save_order($currency_data->id, $input['calc']['local'], $input['calc']['foreign'], $input['calc']['surcharge']);

                $local   = number_format($input['calc']['local'], 2);
                $foreign = number_format($input['calc']['foreign'], 2);

                $response['mail_send'] = FALSE;

                //Send mail if currency is GBP
                if ($currency_data->currency === 'GBP')
                {
                    Orders::send_order_mail($local, $foreign, $currency_data, isset($response['discount']) ? $response['discount']: 0);
                    $response['mail_send'] = TRUE;
                }

                $response['local']      = $local;
                $response['foreign']    = $foreign;
                $response['currency']   = $currency_data->currency;
                $this->message          = "Saved successfully";
                $this->http_status      = 200;
            }
        }
        catch (\Exception $e)
        {
            // TODO:: System admin should be notified that an un expected exception was thrown
            $this->reset_status();
            $this->error = APIErrors::SYSTEM_ERROR;
            Log::error(sprintf("ERROR: %s - Line %s : %s", __METHOD__, __LINE__, $e->getMessage()));
        }

        return $this->formulate_response($response);
    }
}
