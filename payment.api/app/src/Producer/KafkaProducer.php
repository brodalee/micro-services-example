<?php

namespace App\Producer;

use App\Entity\Products;
use RdKafka\Conf;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class KafkaProducer
{
    public function __construct(
        private readonly ParameterBagInterface $params
    )
    {
    }

    public function generateKafkaMessage(string $billingId, string $paymentReference, string $userId, string $type): void
    {
        $conf = new Conf();
        $conf->set('log_level', (string)LOG_DEBUG);
        $conf->set('debug', 'all');

        $producer = new \RdKafka\Producer($conf);
        $producer->addBrokers($this->params->get('KAFKA_CLUSTER_BOOTSTRAP_ENDPOINT'));
        $topicProducer = $producer->newTopic('messages');

        $topicProducer->produce(
            RD_KAFKA_PARTITION_UA,
            0,
            json_encode([
                'table' => 'billings',
                'type' => $type,
                'data' => [
                    'billingId' => $billingId,
                    'paymentReference' => $paymentReference,
                    'userId' => $userId,
                ]
            ])
        );
        $producer->flush(1000);
    }
}