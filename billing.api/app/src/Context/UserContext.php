<?php

namespace App\Context;

use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserContext
{
    private ?string $id = null;
    private ?string $token = null;

    private ?string $email = null;

    public function getId(): string
    {
        if (!$this->id) {
            throw new UnauthorizedHttpException("Not authenticated");
        }

        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }
}