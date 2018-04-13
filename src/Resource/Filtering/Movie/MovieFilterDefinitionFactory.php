<?php

namespace App\Resource\Filtering\Movie;

use App\Resource\Filtering\AbstractFilterDefinitionFactory;
use App\Resource\Filtering\FilterDefinitionFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class MovieFilterDefinitionFactory
    extends AbstractFilterDefinitionFactory
    implements FilterDefinitionFactoryInterface
{
    private const KEY_TITLE = 'title';
    private const KEY_YEAR_FROM = 'yearFrom';
    private const KEY_YEAR_TO = 'yearTo';
    private const KEY_TIME_FROM = 'timeFrom';
    private const KEY_TIME_TO = 'timeTo';
    private const KEY_SORT_BY_QUERY = 'sortBy';
    private const KEY_SORT_BY_ARRAY = 'sortBy';
    private CONST ACCEPTED_SORT_FIELDS = ['id', 'title', 'year', 'time'];

    /**
     * @param Request $request
     * @return MovieFilterDefinition
     */
    public function factory(Request $request): MovieFilterDefinition
    {
        return new MovieFilterDefinition(
            $request->get(self::KEY_TITLE),
            $request->get(self::KEY_YEAR_FROM),
            $request->get(self::KEY_YEAR_TO),
            $request->get(self::KEY_TIME_FROM),
            $request->get(self::KEY_TIME_TO),
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