<?php

namespace App\Dto;

class AddInBasketDto
{
    public function __construct(
        public readonly string $productId
    )
    {
    }
}