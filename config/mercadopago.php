<?php

return [
    'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
    'public_key' => env('MERCADOPAGO_PUBLIC_KEY'),
    'company_email' => env('MERCADOPAGO_COMPANY_EMAIL'),
    'company_fee_percentage' => env('MERCADOPAGO_COMPANY_FEE'), // Comisión de la empresa (porcentaje)
];