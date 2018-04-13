<?php

namespace App\Resource\Filtering\Role;

use App\Repository\RoleRepository;
use App\Resource\Filtering\ResourceFilterInterface;
use Doctrine\ORM\QueryBuilder;

class RoleResourceFilter implements ResourceFilterInterface
{
    /**
     * @var RoleRepository
     */
    private $roleRepository;

    /**
     * RoleResourceFilter constructor.
     * @param RoleRepository $roleRepository
     */
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param $filter
     * @return QueryBuilder
     */
    public function getResources($filter): QueryBuilder
    {
        $qb = $this->getQuery($filter);
        $qb->select('role');

        return $qb;
    }

    /**
     * @param $filter
     * @return QueryBuilder
     */
    public function getResourceCount($filter): QueryBuilder
    {
        $qb = $this->getQuery($filter, 'count');
        $qb->select('count(role)');

        return $qb;
    }

    public function getQuery(RoleFilterDefinition $filter, ?string $count = null): QueryBuilder
    {
        $qb = $this->roleRepository->createQueryBuilder('role');

        if (null !== $filter->getPlayedName()) {
            $qb->where(
                $qb->expr()->like('role.playedName', ':playedName')
            );
            $qb->setParameter('playedName', "%{$filter->getPlayedName()}%");
        }

        if (null !== $filter->getMovie()) {
            $qb->where(
                $qb->expr()->like('role.movie', ':movieId')
            );
            $qb->setParameter('movieId', $filter->getMovie());
        }

        if (null !== $filter->getSortByArray() && $count === null) {
            foreach ($filter->getSortByArray() as $by => $order) {
                $expr = 'desc' == $order
                    ? $qb->expr()->desc("role.$by")
                    : $qb->expr()->asc("role.$by");
                $qb->addOrderBy($expr);
            }
        }

        return $qb;
    }

}