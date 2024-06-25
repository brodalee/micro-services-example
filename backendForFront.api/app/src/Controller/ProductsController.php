<?php

namespace App\Controller;

use App\Context\UserContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        Request $request,
    )
    {
        $page = (int) $request->query->get('page', 0);
        $limit = (int) $request->query->get('limit', 1);
        if ($limit > 100) {
            $limit = 100;
        }

        if ($limit < 1) {
            $limit = 1;
        }

        try {
            $request = $productClientApi->request(
                'GET',
                "products?page=$page&limit=$limit",
                ['headers' => ['Authorization' => 'Bearer ' . $userContext->getToken()]]
            );

            if ($request->getStatusCode() === 200) {
                return $this->json(json_decode($request->getContent()));
            }

            if ($request->getStatusCode() === 401) {
                return $this->json(['error' => 'UnAuthorized'], Response::HTTP_UNAUTHORIZED);
            }
        } catch (\Exception $ex) {
            return $this->json(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(
        string $id,
        HttpClientInterface $productClientApi,
        UserContext $userContext,
    ): Response
    {
        try {
            $request = $productClientApi->request(
                'DELETE',
                "products/$id",
                ['headers' => ['Authorization' => 'Bearer ' . $userContext->getToken()]]
            );

            if ($request->getStatusCode() === 204) {
                return new Response('', Response::HTTP_NO_CONTENT);
            }
        } catch (\Exception $ex) {
            return $this->json(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}