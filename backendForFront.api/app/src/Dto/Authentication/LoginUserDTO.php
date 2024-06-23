<?php

namespace App\Dto\Authentication;

class LoginUserDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password
    )
    {
    }
}