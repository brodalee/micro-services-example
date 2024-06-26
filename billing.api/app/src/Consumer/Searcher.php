<?php

namespace App\Consumer;

class Searcher
{
    public function __construct(
        private readonly BillingsConsumer $billingsConsumer
    )
    {
    }

    public function get(string $tableName, string $type): ?ConsumerInterface
    {
        $consumer = match ($tableName) {
            'billings' => $this->billingsConsumer,
            default => null
        };

        $consumer?->setOperation($type);
        return $consumer;
    }
}