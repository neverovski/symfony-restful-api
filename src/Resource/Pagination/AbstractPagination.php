<?php

namespace App\Resource\Pagination;

use App\Resource\Filtering\FilterDefinitionInterface;
use Doctrine\ORM\UnexpectedResultException;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;

abstract class AbstractPagination implements PaginationInterface
{
    /**
     * @param Page $page
     * @param FilterDefinitionInterface $filter
     * @return PaginatedRepresentation
     */
    public function paginate(Page $page, FilterDefinitionInterface $filter): PaginatedRepresentation
    {
        $resources = $this->getResourceFilter()->getResources($filter)
            ->setFirstResult($page->getOffset())
            ->setMaxResults($page->getLimit())
            ->getQuery()
            ->getResult();

        $resourceCount = $pages = null;

        try {
            $resourceCount = $this->getResourceFilter()->getResourceCount($filter)
                ->getQuery()
                ->getSingleScalarResult();
            $pages = ceil($resourceCount / $page->getLimit());
        } catch (UnexpectedResultException $e) {

        }

        return new PaginatedRepresentation(
            new CollectionRepresentation($resources),
            $this->getRouteName(),
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