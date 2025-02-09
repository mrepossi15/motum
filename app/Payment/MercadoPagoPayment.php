<?php

namespace App\Payment;

use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Resources\Preference;

class MercadoPagoPayment
{
    private string $accessToken;
    private string $publicKey;
    private array $items = [];
    private array $backUrls = [];
    private bool $autoReturn = false;
    private float $applicationFee = 10;
    private string $notificationUrl = '';
    private string $externalReference = '';


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->accessToken = config('mercadopago.access_token');
        $this->publicKey = config('mercadopago.public_key');
       
        if (strlen($this->accessToken) === 0) {
            throw new \Exception('No está definido el token de acceso de Mercado Pago. Configura MERCADOPAGO_ACCESS_TOKEN en el archivo [.env].');
        }
    
        if (strlen($this->publicKey) === 0) {
            throw new \Exception('No está definida la clave pública de Mercado Pago. Configura MERCADOPAGO_PUBLIC_KEY en el archivo [.env].');
        }
    
    
        MercadoPagoConfig::setAccessToken($this->accessToken);
    }

    /**
     * Define los ítems para el cobro.
     */
    public function setItems(array $items)
    {
        $this->items = $items;
    }

    /**
     * Define las URLs de retorno.
     */
    public function setBackUrls(?string $success = null, ?string $pending = null, ?string $failure = null)
    {
        if (is_string($success)) $this->backUrls['success'] = $success;
        if (is_string($pending)) $this->backUrls['pending'] = $pending;
        if (is_string($failure)) $this->backUrls['failure'] = $failure;
    }

    /**
     * Habilita la redirección automática tras el pago.
     */
    public function withAutoReturn()
    {
        $this->autoReturn = true;
    }

    /**
     * Define la comisión para la empresa.
     */
    public function setApplicationFee(float $fee)
    {
        $this->applicationFee = $fee;
    }

    /**
     * Crea la preferencia de cobro con Split Payment.
     */
    public function createSplitPayment(string $trainerCollectorId): Preference
{
    if (count($this->items) === 0) {
        throw new \Exception('Debes definir los ítems del cobro. Usa el método setItems() para asignarlos.');
    }

    // Obtener el Collector ID de la empresa
    $companyCollectorId = config('mercadopago.collector_id');

    $totalAmount = array_reduce($this->items, function ($carry, $item) {
        return $carry + ($item['unit_price'] * $item['quantity']);
    }, 0);

    // Monto del entrenador (descontando la comisión)
    $trainerAmount = $totalAmount - $this->applicationFee;

    $config = [
        'items' => $this->items,
        'payer' => [
            'email' => auth()->user()->email, // Correo del alumno que paga
        ],
        'application_fee' => $this->applicationFee, // Comisión de la empresa
        'back_urls' => $this->backUrls,
        'purpose' => 'wallet_purchase',
        'transfer_to' => [
            [
                'amount' => $trainerAmount,
                'destination' => $trainerCollectorId, // Collector ID del entrenador
            ]
        ],
        'notification_url' => $this->notificationUrl, // Asegurar que Mercado Pago tenga la URL
        'external_reference' => $this->externalReference,
    ];

    if ($this->autoReturn) {
        $config['auto_return'] = 'approved';
    }

    $preferenceFactory = new PreferenceClient();

    try {
        $response = $preferenceFactory->create($config); // Crear preferencia de pago
        \Log::info('Preferencia de Mercado Pago creada:', (array) $response); // Registrar preferencia
        return $response;
    } catch (\Exception $e) {
        \Log::error('Error de Mercado Pago:', [
            'message' => $e->getMessage(),
            'config' => $config, // Registrar datos enviados para depuración
        ]);
        throw $e; // Relanzar excepción para manejarla en el controlador
    }
}


    /**
     * Obtiene la clave pública.
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function setNotificationUrl(string $url)
    {
        $this->notificationUrl = $url;
    }
    public function setExternalReference(string $reference)
{
    $this->externalReference = $reference;
}
}