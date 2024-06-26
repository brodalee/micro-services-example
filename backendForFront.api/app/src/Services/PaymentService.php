<?php

namespace App\Services;

use App\Context\UserContext;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PaymentService
{
    public function __construct(
        private readonly HttpClientInterface $paymentClientApi,
        private readonly UserContext $userContext,
    )
    {
    }

    public function fetchPaymentById(string $id): object
    {
        $request = $this->paymentClientApi->request(
            'GET',
            "payments/$id",
            ['headers' => ['Authorization' => 'Bearer ' . $this->userContext->getToken()]]
        );

        if ($request->getStatusCode() === 200) {
            return json_decode($request->getContent());
        }

        throw new \Exception("Error while retrieving payment");
    }
}