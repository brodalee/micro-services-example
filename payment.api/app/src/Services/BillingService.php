<?php

namespace App\Services;

use App\Context\UserContext;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BillingService
{
    public function __construct(
        private readonly HttpClientInterface $billingClientApi,
        private readonly UserContext $userContext,
    )
    {
    }

    public function fetchBillingPrice(string $billingId): int
    {
        $request = $this->billingClientApi->request(
            'GET',
            'billings/' . $billingId,
            ['headers' => ['Authorization' => 'Bearer ' . $this->userContext->getToken()]]
        );

        if ($request->getStatusCode() === 200) {
            $data = json_decode($request->getContent());
            return $data->totalPrice;
        }

        throw new \Exception('Unable to fetch billing');
    }
}