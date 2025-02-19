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
    private string $notificationUrl = '';
    private string $externalReference = '';

    public function __construct()
    {
        $this->accessToken = config('mercadopago.access_token');
        $this->publicKey = config('mercadopago.public_key');

        if (!$this->accessToken || !$this->publicKey) {
            throw new \Exception('Configura MERCADOPAGO_ACCESS_TOKEN y MERCADOPAGO_PUBLIC_KEY en el archivo .env.');
        }

        MercadoPagoConfig::setAccessToken($this->accessToken);
    }

    public function setItems(array $items)
    {
        $this->items = $items;
    }

    public function setBackUrls(?string $success = null, ?string $pending = null, ?string $failure = null)
    {
        if ($success) $this->backUrls['success'] = $success;
        if ($pending) $this->backUrls['pending'] = $pending;
        if ($failure) $this->backUrls['failure'] = $failure;
    }

    public function withAutoReturn()
    {
        $this->autoReturn = true;
    }
    public function createPayment(): Preference
    {
        if (empty($this->items)) {
            throw new \Exception('Debes definir los ítems del cobro usando setItems().');
        }
    
        $config = [
            'items' => $this->items,
            'payer' => ['email' => auth()->user()->email], // 🔹 Alumno paga
            'back_urls' => $this->backUrls,
            'notification_url' => $this->notificationUrl,
            'external_reference' => $this->externalReference,
            'purpose' => 'wallet_purchase',
            'application_fee' => 0, // 🔥 TODO el dinero llega a la empresa
        ];
    
        \Log::info('📌 Preferencia de pago enviada a Mercado Pago:', $config);
    
        try {
            $preferenceFactory = new PreferenceClient();
            $response = $preferenceFactory->create($config);
            \Log::info('✅ Preferencia de Mercado Pago creada:', (array) $response);
            return $response;
        } catch (\Exception $e) {
            \Log::error('❌ Error en Mercado Pago:', [
                'message' => $e->getMessage(),
                'config' => $config,
            ]);
            throw $e;
        }
    }
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