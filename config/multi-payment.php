<?php

use App\Services\Payment\Gateways\InvoiceGateway;
use App\Services\Payment\Gateways\PaystackGateway;

return [
    'gateways' => [
        'paystack' => [
            'name' => 'Paystack',
            'driver' => PaystackGateway::class
        ],
        'invoice' => [
            'name' => 'Invoice',
            'driver' => InvoiceGateway::class
        ],
    ],
];
