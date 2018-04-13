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
     * @param int|null $movie
     * @return RoleFilterDefinition
     */
    public function factory(Request $request, ?int $movie): RoleFilterDefinition
    {
        return new RoleFilterDefinition(
            $request->get(self::KEY_PLAYED_NAME),
            $movie,
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