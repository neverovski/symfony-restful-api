<?php

namespace App\Resource\Pagination\Role;


use App\Resource\Filtering\Role\RoleResourceFilter;
use App\Resource\Filtering\ResourceFilterInterface;
use App\Resource\Pagination\AbstractPagination;
use App\Resource\Pagination\PaginationInterface;

class RolePagination
    extends AbstractPagination
    implements PaginationInterface
{
    private const ROUTE = 'get_movie_roles';

    /**
     * @var RoleResourceFilter
     */
    private $resourceFilter;

    /**
     * MoviePagination constructor.
     * @param RoleResourceFilter $resourceFilter
     */
    public function __construct(RoleResourceFilter $resourceFilter)
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