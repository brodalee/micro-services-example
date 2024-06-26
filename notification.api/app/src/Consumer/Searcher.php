<?php

namespace App\Consumer;

class Searcher
{
    public function __construct(
        private readonly NotificationConsumer $notificationConsumer,
    )
    {
    }

    public function get(string $tableName, string $type): ?ConsumerInterface
    {
        $consumer = match ($tableName) {
            'notifications' => $this->notificationConsumer,
            default => null
        };

        $consumer?->setOperation($type);
        return $consumer;
    }
}