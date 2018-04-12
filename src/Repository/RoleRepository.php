<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class RoleRepository extends EntityRepository
{
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
