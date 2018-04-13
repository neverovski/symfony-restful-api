<?php

namespace App\Resource\Filtering;

interface SortTableFilterDefinitionInterface
{
    /**
     * @return array|null
     */
    public function getSortByArray(): ?array;

    /**
     * @return null|string
     */
    public function getSortByQuery(): ?string;
}