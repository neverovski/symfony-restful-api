<?php

namespace App\Resource\Filtering;

interface FilterDefinitionInterface
{
    /**
     * @return array
     */
    public function getQueryParameters(): array;

    /**
     * @return array
     */
    public function getQueryParamsBlacklist(): array;

    /**
     * @return array
     */
    public function getParameters(): array;
}