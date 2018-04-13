<?php

namespace App\Resource\Pagination;

use App\Resource\Filtering\FilterDefinitionInterface;
use App\Resource\Filtering\ResourceFilterInterface;
use Hateoas\Representation\PaginatedRepresentation;

interface PaginationInterface
{
    /**
     * @param Page $page
     * @param FilterDefinitionInterface $filter
     * @return PaginatedRepresentation
     */
    public function paginate(Page $page, FilterDefinitionInterface $filter): PaginatedRepresentation;

    /**
     * @return ResourceFilterInterface
     */
    public function getResourceFilter(): ResourceFilterInterface;

    /**
     * @return string
     */
    public function getRouteName(): string;
}