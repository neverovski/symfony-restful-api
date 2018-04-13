<?php

namespace App\Resource\Filtering;

interface FilterDefinitionInterface
{
    public function getQueryParameters(): array;
    public function getQueryParamsBlacklist(): array;
}