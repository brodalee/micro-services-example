<?php

namespace App\Controller;

use App\Context\UserContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/notifications')]
class NotificationController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function fetchLastNotifications(
        HttpClientInterface $notificationClientApi,
        UserContext $userContext,
    ): Response
    {
        $request = $notificationClientApi->request(
            'GET',
            'notifications',
            ['headers' => ['Authorization' => 'Bearer ' . $userContext->getToken()]]
        );

        if ($request->getStatusCode() === 200) {
            return $this->json(
                json_decode($request->getContent())
            );
        }

        return $this->json(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    #[Route('/{id}', methods: ['PATCH'])]
    public function markAsSeen(
        string $id,
        HttpClientInterface $notificationClientApi,
        UserContext $userContext,
    ): Response
    {
        $request = $notificationClientApi->request(
            'PATCH',
            "notifications/$id",
            ['headers' => ['Authorization' => 'Bearer ' . $userContext->getToken()]]
        );

        if ($request->getStatusCode() === 204) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        return $this->json(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}