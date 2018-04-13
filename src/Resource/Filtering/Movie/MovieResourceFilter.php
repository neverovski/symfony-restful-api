<?php

namespace App\Resource\Filtering\Movie;

use App\Repository\MovieRepository;
use Doctrine\ORM\QueryBuilder;

class MovieResourceFilter
{
    /**
     * @var MovieRepository
     */
    private $movieRepository;

    /**
     * MovieResourceFilter constructor.
     * @param MovieRepository $movieRepository
     */
    public function __construct(MovieRepository $movieRepository)
    {
        $this->movieRepository = $movieRepository;
    }

    /**
     * @param MovieFilterDefinition $filter
     * @return QueryBuilder
     */
    public function getResources($filter): QueryBuilder
    {
        $qb = $this->getQuery($filter);
        $qb->select('movie');

        return $qb;
    }

    /**
     * @param MovieFilterDefinition $filter
     * @return QueryBuilder
     */
    public function getResourceCount($filter): QueryBuilder
    {
        $qb = $this->getQuery($filter);
        $qb->select('count(movie)');

        return $qb;
    }

    /**
     * @param MovieFilterDefinition $filter
     * @return QueryBuilder
     */
    public function getQuery(MovieFilterDefinition $filter): QueryBuilder
    {
        $qb = $this->movieRepository->createQueryBuilder('movie');

        if (null !== $filter->getTitle()) {
            $qb->where(
                $qb->expr()->like('movie.title', ':title')
            );
            $qb->setParameter('title', "%{$filter->getTitle()}%");
        }

        if (null !== $filter->getYearFrom()) {
            $qb->andWhere(
                $qb->expr()->gte('movie.year', ':yearFrom')
            );
            $qb->setParameter('yearFrom', $filter->getYearFrom());
        }

        if (null !== $filter->getYearTo()) {
            $qb->andWhere(
                $qb->expr()->lte('movie.year', ':yearTo')
            );
            $qb->setParameter('yearTo', $filter->getYearTo());
        }

        if (null !== $filter->getTimeFrom()) {
            $qb->andWhere(
                $qb->expr()->gte('movie.time', ':timeFrom')
            );
            $qb->setParameter('timeFrom', $filter->getTimeFrom());
        }

        if (null !== $filter->getTimeTo()) {
            $qb->andWhere(
                $qb->expr()->lte('movie.time', ':timeTo')
            );
            $qb->setParameter('timeTo', $filter->getTimeTo());
        }

        if (null !== $filter->getSortByArray()) {
            foreach ($filter->getSortByArray() as $by => $order) {
                $expr = 'desc' == $order
                    ? $qb->expr()->desc("movie.$by")
                    : $qb->expr()->asc("movie.$by");
                $qb->addOrderBy($expr);
            }
        }

        return $qb;
    }

}