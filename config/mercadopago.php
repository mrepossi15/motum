<?php

return [
    'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
    'public_key' => env('MERCADOPAGO_PUBLIC_KEY'),
    'collector_id' => env('MERCADOPAGO_COLLECTOR_ID'), // Collector ID de la empresa
    'company_fee_percentage' => env('MERCADOPAGO_COMPANY_FEE'), // Comisión de la empresa (porcentaje)
];