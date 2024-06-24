<?php

namespace App\Context;

use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserContext
{
    private ?string $id = null;
    private ?string $token = null;

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
        if (!$this->token) {
            throw new UnauthorizedHttpException("Not authenticated");
        }

        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }
}