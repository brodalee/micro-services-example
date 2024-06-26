<?php

namespace App\Consumer;

class BillingsConsumer implements ConsumerInterface
{
    private string $operationType;

    public function consume(object $data): bool
    {
        return match ($this->operationType) {
            'UPDATE' => $this->update($data),
            default => false
        };
    }

    private function update(object $data): bool
    {

    }

    public function setOperation(string $operationType): void
    {
        $this->operationType = $operationType;
    }
}