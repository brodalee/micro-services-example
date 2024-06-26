<?php

namespace App\Dto\Billings;

class CreateBillingItemsDto
{
    public function __construct(
        public readonly string $productId,
        public readonly int $quantity,
    )
    {
    }
}