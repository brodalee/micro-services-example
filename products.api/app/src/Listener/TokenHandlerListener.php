<?php

namespace App\Listener;

use App\Context\UserContext;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TokenHandlerListener
{
    public function __construct(
        private readonly HttpClientInterface $authenticationClientApi,
        private readonly UserContext $userContext,
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
            return;
            //throw new UnauthorizedHttpException("Unauthorized");
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
                $this->userContext->setToken($token);
                return;
            }

            throw new UnauthorizedHttpException('Unauthorized');
        } catch (\Exception $ex) {
            throw new UnauthorizedHttpException('Unauthorized');
        }
    }
}