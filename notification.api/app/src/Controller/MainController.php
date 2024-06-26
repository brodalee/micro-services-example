<?php

namespace App\Controller;

use App\Context\UserContext;
use App\Entity\Notifications;
use App\Repository\NotificationsRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('')]
class MainController extends AbstractController
{
    #[Route('notifications', methods: ['GET'])]
    public function fetchLastNotifications(
        NotificationsRepository $repository,
        UserContext $userContext
    ): Response
    {
        return $this->json(
            $repository->fetchLast($userContext->getId())
        );
    }

    #[Route('notifications/{id}', methods: ['PATCH'])]
    public function markAsSeen(
        #[MapEntity(id: 'id')] Notifications $notification,
        UserContext $userContext,
        NotificationsRepository $repository
    ): Response
    {
        if ($userContext->getId() !== $notification->getUserId()) {
            return $this->json(['error' => 'Invalid user or notification id'], Response::HTTP_BAD_REQUEST);
        }

        $notification->setSeen(true);
        $repository->save($notification);

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}