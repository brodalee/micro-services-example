<?php

namespace App\Dto;

class CreatePaymentDto
{
    public function __construct(
        public readonly string $billingId,
    )
    {
    }
}