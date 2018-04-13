<?php

namespace App\Resource\Filtering;

abstract class AbstractFilterDefinition implements FilterDefinitionInterface
{
    private const QUERY_PARAMS_BLACKLIST = ['sortByArray'];

    /**
     * @return array
     */
    public function getQueryParameters(): array
    {
        return array_diff_key(
            get_object_vars($this),
            array_flip($this->getQueryParamsBlacklist())
        );
    }

    /**
     * @return array
     */
    public function getQueryParamsBlacklist(): array
    {
        return self::QUERY_PARAMS_BLACKLIST;
    }
}