<?php

namespace App\Controller;

use App\Context\UserContext;
use App\Dto\Billings\CreateBillingDto;
use App\Services\PaymentService;
use App\Services\ProductsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/billings')]
class BillingsController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function fetchBillings(
        UserContext $userContext,
        HttpClientInterface $billingsClientApi,
        ProductsService $productsService,
        PaymentService $paymentService,
        Request $request
    ): Response
    {
        $page = $request->query->get('page', 0);
        $limit = $request->query->get('limit', 20);
        if ($limit > 100) {
            $limit = 100;
        }

        if ($limit < 10) {
            $limit = 10;
        }

        $request = $billingsClientApi->request(
            'GET',
            "billings?page=$page&limit=$limit",
            ['headers' => ['Authorization' => 'Bearer ' . $userContext->getToken()]]
        );

        if ($request->getStatusCode() === 200) {
            $data = json_decode($request->getContent());
            foreach ($data->billings as &$billing) {
                foreach ($billing->items as &$item) {
                    $product = $productsService->getProductById($item->productId);
                    if (!$product) {
                        return $this->json(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }

                    $item->productName = $product->designation;
                }

                $payment = $paymentService->fetchPaymentById($billing->paymentReference);
                if (!$payment) {
                    return $this->json(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
                }

                $billing->paymentMethod = $payment->method;
                $billing->isPaymentSucceeded = $payment->isPaymentSucceeded;
            }

            return $this->json($data);
        }

        return $this->json(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}