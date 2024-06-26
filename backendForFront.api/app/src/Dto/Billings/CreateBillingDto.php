<?php

namespace App\Dto\Billings;


class CreateBillingDto
{
    public function __construct(
        /** @var CreateBillingItemsDto[] $products */
        public readonly array $products,
        public readonly float $tva,
    )
    {
    }
}