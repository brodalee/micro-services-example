<?php

namespace App\Controller;

use App\Context\UserContext;
use App\Dto\Basket\AddInBasketDto;
use App\Services\ProductsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/basket')]
class BasketController extends AbstractController
{
    #[Route('/add', methods: ['POST'])]
    public function addInBasket(
        UserContext $userContext,
        #[MapRequestPayload] AddInBasketDto $input,
        HttpClientInterface $basketClientApi,
        SerializerInterface $serializer
    ): Response
    {
        $request = $basketClientApi->request(
            'POST',
            'basket/add-or-create',
            [
                'body' => $serializer->serialize(['productId' => $input->productId], 'json'),
                'headers' => ['Authorization' => 'Bearer ' . $userContext->getToken()]
            ]
        );

        if ($request->getStatusCode() === 200) {
            return $this->json(['status' => 'OK']);
        }

        return $this->json(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    #[Route('/{basketId}', methods: ['DELETE'])]
    public function remove(
        string $basketId,
        UserContext $userContext,
        HttpClientInterface $basketClientApi,
    ): Response
    {
        $request = $basketClientApi->request(
            'DELETE',
            "basket/$basketId/remove",
            [
                'headers' => ['Authorization' => 'Bearer ' . $userContext->getToken()]
            ]
        );

        if ($request->getStatusCode() === 204) {
            return $this->json(['status' => 'OK']);
        }

        return $this->json(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    #[Route('/{basketId}/decrease', methods: ['PATCH'])]
    public function decrease(
        string $basketId,
        UserContext $userContext,
        HttpClientInterface $basketClientApi,
    ): Response
    {
        $request = $basketClientApi->request(
            'PATCH',
            "basket/$basketId/decrease",
            ['headers' => ['Authorization' => 'Bearer ' . $userContext->getToken()]]
        );

        if ($request->getStatusCode() === 204) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        return $this->json(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
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
                $apiProduct = $productsService->getProductById($product->productId);
                if (!$apiProduct) {
                    return $this->json(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
                }

                $finalData[] = [
                    'id' => $apiProduct->id,
                    'name' => $apiProduct->designation,
                    'price' => $apiProduct->price,
                    'reference' => $product->id,
                    'quantity' => $product->quantity
                ];
            }

            return $this->json($finalData);
        }

        return $this->json(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    #[Route('/validate', methods: ['POST'])]
    public function validateBasket(
        UserContext $userContext,
        HttpClientInterface $billingsClientApi,
        HttpClientInterface $basketClientApi,
        HttpClientInterface $paymentClientApi,
        SerializerInterface $serializer,
    ): Response
    {
        $requestBasket = $basketClientApi->request(
            'GET',
            'basket',
            ['headers' => ['Authorization' => 'Bearer ' . $userContext->getToken()]]
        );

        if ($requestBasket->getStatusCode() !== 200) {
            return $this->json(['error' => 'Internal Server Error basket'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $baskets = json_decode($requestBasket->getContent());
        $input = [
            'products' => [],
            'tva' => 20.0
        ];

        foreach ($baskets as $basket) {
            $input['products'][] = [
                'productId' => $basket->productId,
                'quantity' => $basket->quantity
            ];
        }

        $request = $billingsClientApi->request(
            'POST',
            "billings",
            [
                'body' => $serializer->serialize($input, 'json'),
                'headers' => ['Authorization' => 'Bearer ' . $userContext->getToken()]
            ]
        );

        if (!$request->getStatusCode() === 200) {
            return $this->json(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $billingData = json_decode($request->getContent());
        // billingId

        $requestPayment = $paymentClientApi->request(
            'POST',
            'create-payment',
            [
                'body' => $serializer->serialize(['billingId' => $billingData->billingId], 'json'),
                'headers' => ['Authorization' => 'Bearer ' . $userContext->getToken()]
            ]
        );

        if ($request->getStatusCode() === 200) {
            $paymentData = json_decode($requestPayment->getContent());
            return $this->json([
                'billingId' => $billingData->billingId,
                'clientRef' => $paymentData->secret,
                'paymentReference' => $paymentData->paymentReference
            ]);
        }

        return $this->json(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}