<?php

namespace App\Controller;

use App\Dto\LoginUserDTO;
use App\Dto\RegisterUserDTO;
use App\Entity\Users;
use App\Repository\UsersRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
class MainController extends AbstractController
{
    #[Route('register', methods: ['POST'])]
    public function register(
        #[MapRequestPayload] RegisterUserDTO $input,
        UsersRepository $usersRepository,
        UserPasswordHasherInterface $userPasswordHasher,
    ): Response
    {
        $foundUser = $usersRepository->findOneBy(['email' => $input->email]);
        if ($foundUser) {
            return $this->json(['error' => 'User already exists.'], Response::HTTP_BAD_REQUEST);
        }

        $user = new Users();
        $user->setEmail($input->email);
        $user->setPassword(
            $userPasswordHasher->hashPassword($user, $input->password)
        );

        $usersRepository->save($user);
        return $this->json(['status' => 'User created'], Response::HTTP_CREATED);
    }

    #[Route('login', methods: ['POST'])]
    public function login(
        #[MapRequestPayload] LoginUserDTO $input,
        UsersRepository $usersRepository,
        UserPasswordHasherInterface $userPasswordHasher,
        ParameterBagInterface $parameterBag
    ): Response
    {
        $foundUser = $usersRepository->findOneBy(['email' => $input->email]);
        if (!$foundUser) {
            return $this->json(['error' => 'Invalid credentials'], Response::HTTP_BAD_REQUEST);
        }

        if (!$userPasswordHasher->isPasswordValid($foundUser, $input->password)) {
            return $this->json(['error' => 'Invalid credentials'], Response::HTTP_BAD_REQUEST);
        }

        $payload = [
            'iss' => 'https://authentication-micro-service.monsupersite.fr',
            'aud' => 'https://authentication-micro-service.monsupersite.fr',
            'iat' => time(),
            'exp' => time() + 3600,
            'userId' => $foundUser->getId(),
        ];
        $token = JWT::encode($payload, $parameterBag->get('JWT_KEY'), $parameterBag->get('JWT_ALGO'));

        return $this->json(['token' => $token]);
    }

    #[Route('current-user', methods: ['GET'])]
    public function currentUser(
        Request $request,
        UsersRepository $usersRepository,
        ParameterBagInterface $parameterBag
    ): Response
    {

        if (!$request->headers->has('Authorization')) {
            return new Response('UnAuthorized', Response::HTTP_UNAUTHORIZED);
        }

        $token = $request->headers->get('Authorization');
        if (!str_starts_with($token, 'Bearer ')) {
            return new Response('MalFormatted token', Response::HTTP_UNAUTHORIZED);
        }
        $token = str_replace('Bearer ', '', $token);

        try {
            $userId = JWT::decode($token, new Key($parameterBag->get('JWT_KEY'), $parameterBag->get('JWT_ALGO')))->userId;
            $user = $usersRepository->findOneBy(['id' => $userId]);
            if (!$user) {
                return $this->json('User does not exists.', Response::HTTP_NOT_FOUND);
            }

            return $this->json([
                'creationDate' => $user->getCreationDate()->format('Y-m-d'),
                'id' => $user->getId(),
                'email' => $user->getEmail()
            ]);
        } catch (\Exception $ex) {
            return new Response('UnAuthorized', Response::HTTP_UNAUTHORIZED);
        }
    }
}