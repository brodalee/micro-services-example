<?php

namespace App\Dto\Basket;

class AddInBasketDto
{
    public function __construct(
        public readonly string $productId,
    )
    {
    }
}