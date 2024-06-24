<?php

namespace App\Controller;

use App\Context\UserContext;
use App\Dto\AddInBasketDto;
use App\Entity\Basket;
use App\Repository\BasketRepository;
use App\Services\ProductsService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('')]
class MainController extends AbstractController
{
    #[Route('/basket/add-or-create', methods: ['POST'])]
    public function addInBasket(
        #[MapRequestPayload] AddInBasketDto $input,
        BasketRepository $basketRepository,
        UserContext $userContext,
        ProductsService $productsService
    ): Response
    {
        if (!$productsService->productExists($input->productId)) {
            return $this->json(['error' => 'Products does not exists.'], Response::HTTP_NOT_FOUND);
        }

        /** @var Basket $inventory */
        $inventory = $basketRepository->findOneBy([
            'userId' => $userContext->getId(),
            'productId' => $input->productId
        ]);

        if ($inventory) {
            $inventory->setQuantity($inventory->getQuantity() + 1);
            $basketRepository->save($inventory);
            return $this->json(['status' => 'OK']);
        }

        $inventory = new Basket();
        $inventory->setQuantity(1)
            ->setProductId($input->productId)
            ->setUserId($userContext->getId())
        ;

        $basketRepository->save($inventory);
        return $this->json(['status' => 'OK']);
    }

    #[Route('/basket/{id}/remove', methods: ['DELETE'])]
    public function remove(
        #[MapEntity(id: 'id')] Basket $basket,
        BasketRepository $basketRepository,
        UserContext $userContext,
    ): Response
    {
        if ($basket->getUserId() !== $userContext->getId()) {
            return new Response('', Response::HTTP_FORBIDDEN);
        }

        $basketRepository->remove($basket);
        return new Response('', Response::HTTP_NO_CONTENT);
    }

    #[Route('/basket/{id}/decrease', methods: ['PATCH'])]
    public function decrease(
        #[MapEntity(id: 'id')] Basket $basket,
        BasketRepository $basketRepository,
        UserContext $userContext,
    )
    {
        if ($basket->getUserId() !== $userContext->getId()) {
            return new Response('', Response::HTTP_FORBIDDEN);
        }

        if ($basket->getQuantity() === 1) {
            $basketRepository->remove($basket);
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        $basket->setQuantity($basket->getQuantity() - 1);
        $basketRepository->save($basket);

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    #[Route('/basket', methods: ['GET'])]
    public function getBasket(
        UserContext $userContext,
        BasketRepository $basketRepository,
    ): Response
    {
        $baskets = $basketRepository->findBy(['userId' => $userContext->getId()]);
        return $this->json($baskets);
    }
}