<?php

namespace App\Controller;

use App\Context\UserContext;
use App\Dto\Basket\AddInBasketDto;
use App\Services\ProductsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/basket')]
class BasketController extends AbstractController
{
    #[Route('/add', methods: ['POST'])]
    public function addInBasket(
        UserContext $userContext,
        #[MapRequestPayload] AddInBasketDto $input,
        HttpClientInterface $client
    ): Response
    {
    }

    public function remove(): Response
    {
    }

    public function decrease(): Response
    {
    }

    #[Route('', methods: ['GET'])]
    public function getBasket(
        HttpClientInterface $basketClientApi,
        UserContext $userContext,
        ProductsService $productsService,
    ): Response
    {
        $request = $basketClientApi->request(
            'GET',
            'basket',
            ['headers' => ['Authorization' => 'Bearer ' . $userContext->getToken()]]
        );

        if ($request->getStatusCode() === 200) {
            $data = json_decode($request->getContent());
            $finalData = [];
            // TODO : question à poser pour David : Comment optimiser ce morceau de code
            foreach ($data as $product) {
                $apiProduct = $productsService->getProductById($product->id);
                if (!$apiProduct) {
                    return $this->json(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
                }

                $finalData[] = [
                    'id' => $product->id,
                    'name' => $product->designation,
                    'price' => $product->price,
                ];
            }

            return $this->json($finalData);
        }

        return $this->json(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}