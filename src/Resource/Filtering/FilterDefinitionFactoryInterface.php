<?php

namespace App\Resource\Filtering;

interface FilterDefinitionFactoryInterface
{
    /**
     * @param null|string $sortByQuery
     * @return array|null
     */
    public function sortQueryToArray(?string $sortByQuery): ?array;

    /**
     * @return array
     */
    public function getAcceptedSortField(): array;
}