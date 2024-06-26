<?php

namespace App\Repository;

use App\Entity\Notifications;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notifications>
 */
class NotificationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Notifications::class);
    }

    public function fetchLast(string $userId): array
    {
        return $this->createQueryBuilder('n')
            ->select('n')
            ->where('n.userId = :userId')
                ->setParameter('userId', $userId)
            ->andWhere('n.seen = :seen')
                ->setParameter('seen', false)
            ->setFirstResult(0)
            ->setMaxResults(5)
            ->orderBy('n.creationDate', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function save(Notifications $notification): void
    {
        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }
}
