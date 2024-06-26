<?php

namespace App\Consumer;

interface ConsumerInterface
{
    public function consume(object $data): bool;
    public function setOperation(string $operationType): void;
}