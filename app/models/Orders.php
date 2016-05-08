<?php

class Orders extends Eloquent
{
    use \Illuminate\Database\Eloquent\SoftDeletingTrait;

    protected $table = 'orders';

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * Make these things guarded
     */
    protected $guarded = [
        'id',
    ];

    /**
     * Defines a relationship to the Currency Model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function currency()
    {
        return $this->hasOne('Currency', 'currency_id', 'id');
    }

    /**
     * Save order data to database and
     *
     * @param $currency_id
     * @param $zar_amount
     * @param $foreign_amount
     * @param $surcharge_amount
     * @throws Exception
     */
    public static function save_order($currency_id, $zar_amount, $foreign_amount, $surcharge_amount)
    {

        $base = Config::get('mukuru.currency_save_base');

        static::create(
            [
                'currency_id' => $currency_id,
                'zar_amount'  => $zar_amount,  //Store amounts as big integers
                'foreign_amount' => $foreign_amount,
                'surcharge_amount' => $surcharge_amount,
            ]
        );

    }

    /**
     * Send mail to account configured in the mukuru config file app/config/mukuru.php
     *
     * @param $zar_amount
     * @param $foreign_amount
     * @param $currency
     */
    public static function send_order_mail($zar_amount, $foreign_amount, $currency)
    {
        $from_mail  = Config::get('mukuru.from_mail');
        $from_name  = Config::get('mukuru.from_name');
        $to_mail    = Config::get('mukuru.to_mail');
        $to_name    = Config::get('mukuru.to_name');

        Mail::send('emails.mukuru', [
            'local_amount'      => $zar_amount,
            'foreign_amount'    => $foreign_amount,
            'cur'               => $currency
        ], function($message) use ($from_mail, $from_name, $to_mail, $to_name)
        {
            $message->from($from_mail, $from_name);
            $message->to($to_mail, $to_name)->subject('Mukuru purchase order success');
        });
    }

    /**
     * Calculate exchange rate
     *
     * @param float $amount
     * @param float $rate
     * @param float $surcharge
     * @param string $amount_type
     * @return mixed
     */
    public static function calculate_rates($amount, $rate, $surcharge, $amount_type = 'ZAR')
    {
        $surcharge_rate = ((100 * $rate) - ($rate * $surcharge)) / 100;

        if ($amount_type !== 'ZAR')
        {
            $result['foreign']      = $amount;
            $result['local']        = $amount / $surcharge_rate;
        }
        else
        {
            $result['local']        = $amount;
            $result['foreign']      = $result['local'] * $surcharge_rate;
        }

        $result['surcharge']    = (($result['local'] * $rate) - $result['foreign']) / $rate;

        return $result;
    }
}