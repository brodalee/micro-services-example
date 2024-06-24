<?php

namespace App\Controller;

use App\Context\UserContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/products')]
class ProductsController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function fetchAll(
        HttpClientInterface $productClientApi,
        UserContext $userContext,
    )
    {
        try {
            $request = $productClientApi->request(
                'GET',
                'products',
                ['headers' => ['Authorization' => 'Bearer ' . $userContext->getToken()]]
            );

            if ($request->getStatusCode() === 200) {
                return $this->json(json_decode($request->getContent()));
            }
        } catch (\Exception $ex) {
            return $this->json(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}