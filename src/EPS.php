<?php

namespace Sixersoft\EPSPayment;

use Illuminate\Support\Facades\Http;

class EPS
{
    protected $baseUrl;
    protected $userName;
    protected $password;
    protected $deviceTypeId;
    protected $hashkey;
    protected $merchant_id;
    protected $store_id;

    public function __construct()
    {
        $this->baseUrl = config('eps.base_url');
        $this->userName = config('eps.username');
        $this->password = config('eps.password');
        $this->deviceTypeId = config('eps.device_type_id');
        $this->hashkey = config('eps.hashkey');
        $this->merchant_id = config('eps.merchant_id');
        $this->store_id = config('eps.store_id');
    }

    private function generateHash($payload)
    {
        return base64_encode(hash_hmac('sha512', $payload, $this->hashkey, true));
    }

    public function getToken()
    {
        $response = Http::withHeaders([
            'x-hash' => $this->generateHash($this->userName),
            'Content-Type' => 'application/json'
        ])->post("$this->baseUrl/v1/Auth/GetToken", [
            "userName" => $this->userName,
            "password" => $this->password
        ]);

        return $response->json();
    }

    public function createPayment($payload = [])
    {
        $tokenResponse = $this->getToken();
        if (!isset($tokenResponse['token'])) {
            throw new \Exception("Failed to retrieve access token.");
        }

        $invoiceId = (string) time();
        $payload = array_merge([
            "deviceTypeId" => $this->deviceTypeId,
            "merchantId" => $this->merchant_id,
            "storeId" => $this->store_id,
            "transactionTypeId" => 1,
            "financialEntityId" => 0,
            "version" => "1",
            "transactionDate" => now()->toIso8601String(),
            "transitionStatusId" => 0,
            "valueD" => "",
            "merchantTransactionId" => $invoiceId,
        ], $payload);

        $response = Http::withHeaders([
            'x-hash' => $this->generateHash($invoiceId),
            'Authorization' => 'Bearer ' . $tokenResponse['token'],
            'Content-Type' => 'application/json'
        ])->post("$this->baseUrl/v1/EPSEngine/InitializeEPS", $payload);

        return $response->json();
    }
}
