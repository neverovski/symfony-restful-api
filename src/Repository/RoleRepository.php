<?php

namespace App\Repository;

use App\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class RoleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Role::class);
    }

    /**
     * @param int $movieId
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCountMovie(int $movieId): int
    {
        $qb = $this->createQueryBuilder('r');
        return $qb->select('count(r.id)')
                    ->where('r.movie = :movieId')
                    ->setParameter('movieId', $movieId)
                    ->getQuery()
                    ->getSingleScalarResult();
    }
}
