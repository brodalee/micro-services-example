<?php

namespace App\Dto;

class CreateBillingItemsDto
{
    public function __construct(
        public readonly string $productId,
        public readonly int $quantity,
    )
    {
    }
}