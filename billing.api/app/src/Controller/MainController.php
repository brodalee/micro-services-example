<?php

namespace App\Controller;

use App\Context\UserContext;
use App\Dto\CreateBillingDto;
use App\Entity\Billings;
use App\Entity\BillingsItems;
use App\Producer\KafkaProducer;
use App\Repository\BillingsRepository;
use App\Services\ProductsService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('')]
class MainController extends AbstractController
{
    #[Route('billings', methods: ['POST'])]
    public function createBilling(
        UserContext $userContext,
        #[MapRequestPayload] CreateBillingDto $input,
        ProductsService $productsService,
        BillingsRepository $repository,
        KafkaProducer $producer,
    ): Response
    {
        $billing = new Billings();
        $billing->setUserId($userContext->getId())
            ->setTva($input->tva);

        foreach ($input->products as $product) {
            $apiProduct = $productsService->getProductById($product->productId);
            if (!$apiProduct) {
                return $this->json(['error' => "Product with id $product->productId does not exists"], Response::HTTP_BAD_REQUEST);
            }

            $item = new BillingsItems();
            $item->setBilling($billing)
                ->setPrice($apiProduct->price)
                ->setQuantity($product->quantity)
                ->setProductId($product->productId)
            ;
            $billing->addItem($item);
        }

        $repository->save($billing);
        $producer->generateKafkaMessage($userContext->getId(), 'CLEAR');

        return $this->json(['billingId' => $billing->getId()]);
    }

    #[Route('billings', methods: ['GET'])]
    public function fetchBillings(
        UserContext $userContext,
        BillingsRepository $repository,
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

        $totalCount = $repository->count(['userId' => $userContext->getId()]);
        $billings = $repository->fetchPaginated($userContext->getId(), $page, $limit);
        $totalPage = (int) ceil($totalCount / $limit);

        if ($totalPage === $page || $page > $totalPage) {
            $nextPage = null;
        } else {
            $nextPage = $page + 1;
        }

        if ($page === 0) {
            $previousPage = null;
        } else {
            $previousPage = $page - 1;
        }

        return $this->json(
            [
                'totalCount' => $totalCount,
                'totalPage' => $totalPage,
                'nextPage' => $nextPage,
                'previousPage' => $previousPage,
                'billings' => array_map(
                    fn (Billings $billing) => [
                        'id' => $billing->getId(),
                        'totalPrice' => $billing->getTotalPrice(),
                        'tva' => $billing->getTva(),
                        'creationDate' => $billing->getCreationDate()->format('Y-m-d H:i:s'),
                        'paymentReference' => $billing->getPaymentReference(),
                        'items' => array_map(
                            fn (BillingsItems $item) => [
                                'id' => $item->getId(),
                                'price' => $item->getPrice(),
                                'productId' => $item->getProductId(),
                                'quantity' => $item->getQuantity(),
                            ],
                            $billing->getItems()->toArray()
                        )
                    ],
                    $billings
                )
            ]
        );
    }

    #[Route('billings/{id}', methods: ['GET'])]
    public function fetchBillingById(
        #[MapEntity(id: 'id')] Billings $billing,
        UserContext $userContext
    ): Response
    {
        if ($userContext->getId() !== $billing->getUserId()) {
            return $this->json(['error' => 'Bad userId or billingId'], Response::HTTP_BAD_REQUEST);
        }

        $data = [
            'id' => $billing->getId(),
            'totalPrice' => $billing->getTotalPrice(),
            'tva' => $billing->getTva(),
            'creationDate' => $billing->getCreationDate()->format('Y-m-d H:i:s'),
            'paymentReference' => $billing->getPaymentReference(),
            'items' => array_map(
                fn (BillingsItems $item) => [
                    'id' => $item->getId(),
                    'price' => $item->getPrice(),
                    'productId' => $item->getProductId(),
                    'quantity' => $item->getQuantity(),
                ],
                $billing->getItems()->toArray()
            )
        ];

        return $this->json($data);
    }
}