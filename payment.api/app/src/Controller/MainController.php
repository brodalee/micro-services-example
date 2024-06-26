<?php

namespace App\Controller;

use App\Context\UserContext;
use App\Dto\CreatePaymentDto;
use App\Entity\PaymentMethod;
use App\Entity\Payments;
use App\Entity\PaymentStatus;
use App\Producer\KafkaProducer;
use App\Repository\PaymentsRepository;
use App\Services\BillingService;
use Stripe\StripeClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('')]
class MainController extends AbstractController
{
    #[Route('create-payment', methods: ['POST'])]
    public function createPayment(
        UserContext $userContext,
        #[MapRequestPayload] CreatePaymentDto $input,
        StripeClient $client,
        PaymentsRepository $repository,
        BillingService $billingService,
        KafkaProducer $producer
    ): Response
    {
        $email = $userContext->getEmail();
        $customers = $client->customers->search([
            'query' => "email:'$email'"
        ]);

        if (count($customers) === 1) {
            $customer = $customers->first();
        } else if (count($customers) === 0) {
            $customer = $client->customers->create([
                'email' => $email,
            ]);
        } else {
            return $this->json(['error' => 'Too many customers with same email'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $billingTotalPrice = $billingService->fetchBillingPrice($input->billingId);

        $intent = $client->paymentIntents->create([
            'customer' => $customer->id,
            'amount' => $billingTotalPrice,
            'currency' => 'eur',
            'payment_method_types' => ['card'],
            'metadata' => [
                'billingId' => $input->billingId,
                'userId' => $userContext->getId(),
            ],
        ]);

        $payment = new Payments();
        $payment->setUserId($userContext->getId())
            ->setStatus(PaymentStatus::WAITING_FOR_PAYMENT)
            ->setMethod(PaymentMethod::CARD)
            ->setBillingId($input->billingId)
        ;
        $repository->save($payment);
        $producer->generateKafkaMessage(
            $input->billingId,
            $payment->getId(),
            $userContext->getId(),
            'UPDATE'
        );

        return $this->json([
            'secret' => $intent->client_secret,
            'paymentReference' => $payment->getId(),
        ]);
    }
}