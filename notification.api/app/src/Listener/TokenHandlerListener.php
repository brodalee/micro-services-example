<?php

namespace App\Listener;

use App\Context\UserContext;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TokenHandlerListener
{
    public function __construct(
        private readonly HttpClientInterface $authenticationClientApi,
        private readonly UserContext $userContext,
        private readonly LoggerInterface $logger
    )
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$event->isMainRequest()) {
            return;
        }

        if (!$request->headers->has('Authorization')) {
            throw new UnauthorizedHttpException("Unauthorized");
        }

        $header = $request->headers->get('Authorization');
        if (!str_starts_with($header, 'Bearer ')) {
            throw new UnauthorizedHttpException('MalFormatted Authorization header');
        }

        $token = str_replace('Bearer ', '', $header);

        try {
            $request = $this->authenticationClientApi->request(
                'GET',
                'current-user',
                [
                    'headers' => [
                        'Authorization' => $header
                    ]
                ]
            );

            if ($request->getStatusCode() === 200) {
                $data = json_decode($request->getContent());
                $this->userContext->setToken($token);
                $this->userContext->setId($data->id);
                return;
            }

            throw new UnauthorizedHttpException('Unauthorized');
        } catch (\Exception $ex) {
            throw new UnauthorizedHttpException('Unauthorized');
        }
    }
}