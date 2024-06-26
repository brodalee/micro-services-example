<?php

namespace App\Consumer;

use App\Producer\KafkaProducer;
use App\Repository\BasketRepository;

class ProductConsumer implements ConsumerInterface
{
    private string $operationType;

    public function __construct(
        private readonly BasketRepository $basketRepository,
        private readonly KafkaProducer $kafkaProducer,
    )
    {
    }

    public function consume(object $data): bool
    {
        return match ($this->operationType) {
            'DELETE' => $this->delete($data->id, $data->name),
            default => false
        };
    }

    private function delete(string $productId, string $name): bool
    {
        $baskets = $this->basketRepository->findBy(['productId' => $productId]);
        foreach ($baskets as $basket) {
            $this->basketRepository->remove($basket);
            $this->kafkaProducer->generateKafkaMessage(
                "Le produit $name n'est plus disponible à la vente et à donc été supprimé de votre panier.",
                $basket->getUserId(),
                'CREATE'
            );
        }

        return true;
    }

    public function setOperation(string $operationType): void
    {
        $this->operationType = $operationType;
    }
}