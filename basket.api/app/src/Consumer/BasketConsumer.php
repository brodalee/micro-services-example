<?php

namespace App\Consumer;

use App\Repository\BasketRepository;

class BasketConsumer implements ConsumerInterface
{
    private string $operationType;

    public function __construct(
        private readonly BasketRepository $basketRepository,
    )
    {
    }

    public function consume(object $data): bool
    {
        return match ($this->operationType) {
            'CLEAR' => $this->clearBasket($data->userId),
            default => false
        };
    }

    private function clearBasket(string $userId): bool
    {
        $baskets = $this->basketRepository->findBy(['userId' => $userId]);
        foreach ($baskets as $basket) {
            $this->basketRepository->remove($basket);
        }

        return true;
    }

    public function setOperation(string $operationType): void
    {
        $this->operationType = $operationType;
    }
}