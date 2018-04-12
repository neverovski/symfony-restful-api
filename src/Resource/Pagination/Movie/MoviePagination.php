<?php

namespace App\Resource\Pagination\Movie;

use App\Resource\Filtering\Movie\MovieFilterDefinition;
use App\Resource\Filtering\Movie\MovieResourceFilter;
use App\Resource\Pagination\Page;
use Doctrine\ORM\UnexpectedResultException;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;

class MoviePagination
{
    /**
     * @var MovieResourceFilter
     */
    private $movieResourceFilter;

    /**
     * MoviePagination constructor.
     * @param MovieResourceFilter $movieResourceFilter
     */
    public function __construct(MovieResourceFilter $movieResourceFilter)
    {
        $this->movieResourceFilter = $movieResourceFilter;
    }

    public function paginate(Page $page, MovieFilterDefinition $filter): PaginatedRepresentation
    {
        $resources = $this->movieResourceFilter->getResources($filter)
            ->setFirstResult($page->getOffset())
            ->setMaxResults($page->getLimit())
            ->getQuery()
            ->getResult();

        $resourceCount = $pages = null;

        try {
            $resourceCount = $this->movieResourceFilter->getResourceCount($filter)
                ->getQuery()
                ->getSingleScalarResult();
            $pages = ceil($resourceCount / $page->getLimit());
        } catch (UnexpectedResultException $e) {

        }

        return new PaginatedRepresentation(
            new CollectionRepresentation($resources),
            'get_movies',
            $filter->getQueryParameters(),
            $page->getPage(),
            $page->getLimit(),
            $pages,
            null,
            null,
            false,
            $resourceCount
        );

    }
}