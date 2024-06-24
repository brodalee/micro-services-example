<?php

namespace App\Repository;

use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Products>
 */
class ProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Products::class);
    }

    public function fetchPaginated(int $page, int $limit)
    {
        return $this->createQueryBuilder('p')
            ->select('p')
            ->orderBy('p.creationDate', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($page * $limit)
            ->getQuery()
            ->getResult()
        ;
    }
}
