<?php

namespace App\Controller;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('')]
class MainController extends AbstractController
{
    #[Route('/products', methods: ['GET'])]
    public function fetchProducts(
        Request $request,
        ProductsRepository $productsRepository,
        SerializerInterface $serializer
    ): Response
    {
        $page = (int) $request->query->get('page', 0);
        $limit = (int) $request->query->get('limit', 1);
        if ($limit > 100) {
            $limit = 100;
        }

        if ($limit < 1) {
            $limit = 1;
        }

        $count = $productsRepository->count();

        if ($count === 0) {
            return $this->json([
                'totalPage' => 0,
                'totalCount' => 0,
                'nextPage' => null,
                'previousPage' => null,
                'products' => []
            ]);
        }

        $products = $productsRepository->fetchPaginated($page, $limit);
        $totalPage = (int) ceil($count / $limit);
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

        return $this->json([
            'totalPage' => $totalPage,
            'totalCount' => $count,
            'nextPage' => $nextPage,
            'previousPage' => $previousPage,
            'products' => $products
        ]);
    }

    #[Route('products/{id}', methods: ['GET'])]
    public function fetchProduct(
        #[MapEntity(id: 'id')] Products $product
    ): Response
    {
        return $this->json($product);
    }
}