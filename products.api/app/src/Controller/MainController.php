<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
class MainController extends AbstractController
{
    #[Route('products', methods: ['GET'])]
    public function fetchProducts(
        Request $request
    )
    {
        $page = $request->query->get('page', 0);
        $limit = $request->query->get('limit', 0);
        if ($limit > 100) {
            $limit = 100;
        }

        // TODO fetch paginée des produits.
    }

    #[Route('products/{id}', methods: ['GET'])]
    public function fetchProduct(string $id)
    {
        // TODO fetch single product.
    }
}