<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'mandrill' => [
        'secret' => env('MANDRILL_SECRET'),
    ],

    'ses' => [
        'key'    => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'stripe' => [
        'model'  => App\User::class,
        'key'    => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
    'linkedin' => [
		'client_id' => '758zprn7cul855',
		'client_secret' => 'of5uVVM5iG3KyPL7',
		'redirect' => 'http://'.$_SERVER['HTTP_HOST'].'/account/linkedin/',
	],
	'facebook' => [
		'client_id' => '438386079698694',
		'client_secret' => '77e2feeb47d38b9f2f3c2ff30949430d',
		'redirect' => 'http://'.$_SERVER['HTTP_HOST'].'/account/facebook/',
	],
	'google' => [
		'client_id' => '955274654850-3hqsmda21rc6tkrm5cnp5v9egd34pijv.apps.googleusercontent.com',
		'client_secret' => '04q5NUfvOi9pHZQvOwXEDWdy',
		'redirect' => 'http://'.$_SERVER['HTTP_HOST'].'/account/google/',
	],

];
