<?php

namespace App\Repository;

use App\Entity\Basket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Basket>
 */
class BasketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Basket::class);
    }

    public function save(Basket $basket): void
    {
        $this->entityManager->persist($basket);
        $this->entityManager->flush();
    }

    public function remove(Basket $basket): void
    {
        $this->entityManager->remove($basket);
        $this->entityManager->flush();
    }
}
