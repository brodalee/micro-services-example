<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserDTO
{
    public function __construct(
        #[Assert\Email]
        public readonly string $email,
        #[Assert\Length(min: 3)]
        #[Assert\NotBlank]
        public readonly string $password
    )
    {
    }
}