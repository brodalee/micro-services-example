<?php

namespace App\Dto\Authentication;

class RegisterUserDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password
    )
    {
    }
}