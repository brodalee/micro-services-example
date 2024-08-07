<?php

namespace App\Consumer;

class Searcher
{
    public function __construct(
        private readonly ProductConsumer $productConsumer,
        private readonly BasketConsumer $basketConsumer,
    )
    {
    }

    public function get(string $tableName, string $type): ?ConsumerInterface
    {
        $consumer = match ($tableName) {
            'products' => $this->productConsumer,
            'baskets' => $this->basketConsumer,
            default => null
        };

        $consumer?->setOperation($type);
        return $consumer;
    }
}