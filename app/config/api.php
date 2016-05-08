<?php

return [
    /*
     * Stores the URL that the API resides at. All API calls will be made against this URL.
     */
    'url' => 'http://api.mukuru.test/',
    /*
     *
     * Stores the API secret that is to be used when accessing the API instance residing at the URL supplied in the 'url' element.
     */
    'secret' => 'jIDSJ8*(DFSBm2#@%',
    /*
     * Stores the API key that is to be used when accessing the API instance residing at the URL supplied in the 'url' element.
     */
    'key' => 'k:JDKSisfe8969J#$mv*79',
    /*
     * Stores the version of the API currently being used
     */
    'version' => 'v1',

    /*
     * Verify if ssl is being used
     */
    'ssl' => FALSE,

    /*
     * Url for the external exchange rate api, currently using Oanda as its the only one I could find that include KENYA(1 month trail only)
     */
    'external_api' => 'https://www.oanda.com/',

    /*
     * Currency api path used to retrieve the currency
     */
    'external_api_path' => 'rates/api/v1/rates/ZAR.json',

    /*
     * Key for the external api
     */
    'external_api_key' => 'BRYu4eCikg4gOyaofFHB0bFk',

    /*
     * Currency base
     */
    'external_api_base' => 'ZAR',

    /*
     * Max decimal
     */
    'external_api_max_decimals' => 8,

    /*
     * Currencies to retrieve
     */
    'external_foreign_currencies' => ['USD', 'GBP', 'EUR', 'KES'],
];
