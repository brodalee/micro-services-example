<?php

namespace App\Services;

use App\Context\UserContext;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProductsService
{
    public function __construct(
        private readonly HttpClientInterface $productClientApi,
        private readonly UserContext $userContext,
    )
    {
    }

    public function getProductById(string $productId): object|null
    {
        try {
            $request = $this->productClientApi->request(
                'GET',
                'products/' . $productId,
                ['headers' => ['Authorization' => 'Bearer ' . $this->userContext->getToken()]]
            );

            if ($request->getStatusCode() === 200) {
                return json_decode($request->getContent());
            }

            return null;
        } catch (\Exception $ex) {
            return null;
        }
    }

    public function productExists(string $productId): bool
    {
        return $this->getProductById($productId) !== null;
    }
}