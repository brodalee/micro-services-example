<?php

namespace App\Consumer;

class Searcher
{
    public function __construct(
        private readonly ProductConsumer $productConsumer,
    )
    {
    }

    public function get(string $tableName, string $type): ConsumerInterface
    {
        $consumer = match ($tableName) {
            'products' => $this->productConsumer,
        };

        $consumer->setOperation($type);
        return $consumer;
    }
}