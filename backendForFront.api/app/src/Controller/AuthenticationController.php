<?php

namespace App\Controller;

use App\Context\UserContext;
use App\Dto\Authentication\LoginUserDTO;
use App\Dto\Authentication\RegisterUserDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/authentication')]
class AuthenticationController extends AbstractController
{
    #[Route('/register', methods: ['POST'])]
    public function register(
        #[MapRequestPayload] RegisterUserDTO $input,
        HttpClientInterface $authenticationClientApi,
        SerializerInterface $serializer
    ): Response
    {
        $request = $authenticationClientApi->request(
            'POST',
            'register',
            [
                'body' => $serializer->serialize($input, 'json'),
            ],
        );

        if ($request->getStatusCode() === 201) {
            return $this->json(json_decode($request->getContent()));
        }

        if ($request->getStatusCode() === 400) {
            return $this->json(['error' => 'Bad request'], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    #[Route('/login', methods: ['POST'])]
    public function login(
        #[MapRequestPayload] LoginUserDTO $input,
        HttpClientInterface $authenticationClientApi,
        SerializerInterface $serializer
    ): Response
    {
        $request = $authenticationClientApi->request(
            'POST',
            'login',
            [
                'body' => $serializer->serialize($input, 'json'),
            ],
        );

        if ($request->getStatusCode() === 200) {
            return $this->json(json_decode($request->getContent()));
        }

        if ($request->getStatusCode() === 400) {
            return $this->json(['error' => 'Bad credentials'], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}