<?php

namespace App\Consumer;

use App\Entity\Notifications;
use App\Repository\NotificationsRepository;

class NotificationConsumer implements ConsumerInterface
{
    private string $operationType;

    public function __construct(
        private readonly NotificationsRepository $repository,
    )
    {
    }

    public function consume(object $data): bool
    {
        return match ($this->operationType) {
            'CREATE' => $this->create($data->message, $data->userId),
            default => false
        };
    }

    private function create(string $message, string $userId): bool
    {
        $notification = new Notifications();
        $notification->setSeen(false)
            ->setUserId($userId)
            ->setMessage($message)
        ;
        $this->repository->save($notification);

        return true;
    }

    public function setOperation(string $operationType): void
    {
        $this->operationType = $operationType;
    }
}