<?php

namespace App\Repository;

use App\Entity\Billings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Billings>
 */
class BillingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Billings::class);
    }

    public function save(Billings $billing): void
    {
        $this->entityManager->persist($billing);
        $this->entityManager->flush();
    }

    public function fetchPaginated(string $userId, int $page, int $limit): array
    {
        return $this->createQueryBuilder('b')
            ->select('b')
            ->where('b.userId = :userId')
                ->setParameter('userId', $userId)
            ->orderBy('b.creationDate', 'DESC')
            ->setFirstResult($page * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }
}
