<?php

namespace App\Resource\Filtering\Role;

use App\Resource\Filtering\AbstractFilterDefinitionFactory;
use App\Resource\Filtering\FilterDefinitionFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class RoleFilterDefinitionFactory
    extends AbstractFilterDefinitionFactory
    implements FilterDefinitionFactoryInterface
{
    private const KEY_PLAYED_NAME = 'playedName';
    private const KEY_MOVIE = 'movie';
    private const KEY_SORT_BY_QUERY = 'sortBy';
    private const KEY_SORT_BY_ARRAY = 'sortBy';
    private CONST ACCEPTED_SORT_FIELDS = ['id', 'playedName', 'movie'];

    /**
     * @param Request $request
     * @return RoleFilterDefinition
     */
    public function factory(Request $request): RoleFilterDefinition
    {
        return new RoleFilterDefinition(
            $request->get(self::KEY_PLAYED_NAME),
            $request->get(self::KEY_MOVIE),
            $request->get(self::KEY_SORT_BY_QUERY),
            $this->sortQueryToArray($request->get(self::KEY_SORT_BY_ARRAY))
        );
    }

    /**
     * @return array
     */
    public function getAcceptedSortField(): array
    {
        return self::ACCEPTED_SORT_FIELDS;
    }
}