<?php

use \Illuminate\Database\Eloquent\SoftDeletingTrait;

class Currency extends Eloquent
{
    use SoftDeletingTrait;

    /**
     * Table association
     *
     * @var string
     */
    protected $table = 'currency';

    /**
     * Required for soft deletes
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * Fillable fields
     *
     * @var array
     */
    protected $fillable = [
        'display_name',
        'currency',
        'rate',
        'surcharge',
        'discount',
        'created_at',
        'updated_at',
    ];

    /**
     * Defines a relationship to the Orders Model
     */
    public function orders()
    {
        return $this->hasMany('Orders', 'id', 'currency_id');
    }

    public static function update_currency($currency, $rate)
    {
        //Check if there is a record to update
        $record = static::where('currency', '=', $currency)->first();

        if (!$record)
        {
            Log::error(sprintf('Currency [%s] does not exist, could not update to %s', $currency, $rate));
            return FALSE;
        }

        //Create a new record for the update data
        static::create(
            [
                'display_name'  => $record->display_name,
                'currency'      => $record->currency,
                'rate'          => $rate,
                'surcharge'     => $record->surcharge,
                'discount'      => $record->discount
            ]
        );

        //Add the deleted timestamp to the old record
        $record->destroy($record->id);

        return TRUE;
    }
}