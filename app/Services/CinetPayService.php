<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CinetPayService
{
    private $apiKey;
    private $siteId;
    private $baseUrl;
    
    public function __construct()
    {
        $this->apiKey = config('services.cinetpay.api_key');
        $this->siteId = config('services.cinetpay.site_id');
        $this->baseUrl = 'https://api-checkout.cinetpay.com/v2/payment';
    }
    
    public function initiatePayment($amount, $transactionId, $customerName, $customerEmail)
    {
        $data = [
            'apikey' => $this->apiKey,
            'site_id' => $this->siteId,
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'currency' => 'XOF',
            'description' => 'Paiement pour services de styliste',
            'customer_name' => $customerName,
            'customer_email' => $customerEmail,
            'channels' => 'ALL',
            'notify_url' => route('api.payment.notify'),
            'return_url' => route('api.payment.return'),
            'lang' => 'fr',
        ];

        try {
            $response = Http::post($this->baseUrl, $data);
            return $response->json();
        } catch (\Exception $e) {
            \Log::error('CinetPay Error: ' . $e->getMessage());
            throw new \Exception('Erreur lors de l\'initialisation du paiement');
        }
    }

    public function verifyPayment($transactionId)
    {
        $data = [
            'apikey' => $this->apiKey,
            'site_id' => $this->siteId,
            'transaction_id' => $transactionId
        ];

        try {
            $response = Http::post($this->baseUrl . '/check', $data);
            return $response->json();
        } catch (\Exception $e) {
            \Log::error('CinetPay Verification Error: ' . $e->getMessage());
            throw new \Exception('Erreur lors de la vérification du paiement');
        }
    }
}