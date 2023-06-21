<?php

/**
 * Config Array
 * Static and General configuration for the integration
 * Constant Parameters.
 */

return [
    'ENV' => 'TEST',
    'TEST' => [
        'baseUrl' => 'https://api-sandbox.dhl.com',
        'apiKey' => 'demo-key',
        ''
    ],

    'LIVE' => [
        'AccountNumber'		 	=> '71503496',
        'UserName'			 	=> 'enquiries@parcelsmartsolutions.com',
        'Password'			 	=> 'Hopefull12345@',
        'AccountPin'		 	=> '726593',
        'AccountEntity'		 	=> 'LOS',
        'AccountCountryCode'	=> 'NG',
        'Version'			 	=> 'v1'
    ],

    'CompanyName' => 'Moustafa Allahham',
    'ProductGroup' => 'EXP',
    'ProductType' => 'PPX',
    'Payment' => 'P',
    'PaymentOptions' => null,
    'Services' => null,
    'CurrencyCode' => 'USD',
    'LabelInfo' => [
        'ReportID' 		=> 9201,
        'ReportType'	=> 'URL',
    ]
];
