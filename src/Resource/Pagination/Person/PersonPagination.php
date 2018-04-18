<?php

namespace App\Resource\Pagination\Person;

use App\Resource\Filtering\Person\PersonResourceFilter;
use App\Resource\Filtering\ResourceFilterInterface;
use App\Resource\Pagination\AbstractPagination;
use App\Resource\Pagination\PaginationInterface;

class PersonPagination
    extends AbstractPagination
    implements PaginationInterface
{
    private const ROUTE = 'get_persons';

    /**
     * @var PersonResourceFilter
     */
    private $resourceFilter;

    /**
     * MoviePagination constructor.
     * @param PersonResourceFilter $resourceFilter
     */
    public function __construct(PersonResourceFilter $resourceFilter)
    {
        $this->resourceFilter = $resourceFilter;
    }
    /**
     * @return ResourceFilterInterface
     */
    public function getResourceFilter(): ResourceFilterInterface
    {
        return $this->resourceFilter;
    }

    /**
     * @return string
     */
    public function getRouteName(): string
    {
        return self::ROUTE;
    }
}