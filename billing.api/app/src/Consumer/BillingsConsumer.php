<?php

namespace App\Consumer;

use App\Repository\BillingsRepository;

class BillingsConsumer implements ConsumerInterface
{
    private string $operationType;

    public function __construct(
        private readonly BillingsRepository $repository
    )
    {
    }

    public function consume(object $data): bool
    {
        return match ($this->operationType) {
            'UPDATE' => $this->update($data),
            default => false
        };
    }

    private function update(object $data): bool
    {
        $billing = $this->repository->findOneBy(['userId' => $data->userId, 'id' => $data->billingId]);
        if ($billing) {
            $billing->setPaymentReference($data->paymentReference);
            $this->repository->save($billing);
        }

        return true;
    }

    public function setOperation(string $operationType): void
    {
        $this->operationType = $operationType;
    }
}