<?php

namespace App\Controller;

use App\Context\UserContext;
use App\Dto\Billings\CreateBillingDto;
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
            return $this->json(
                json_decode($request->getContent())
            );
        }

        return $this->json(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}