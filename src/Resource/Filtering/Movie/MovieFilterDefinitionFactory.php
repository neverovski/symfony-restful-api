<?php

namespace App\Resource\Filtering\Movie;

use Symfony\Component\HttpFoundation\Request;

class MovieFilterDefinitionFactory
{
    private const KEY_TITLE = 'title';
    private const KEY_YEAR_FROM = 'yearFrom';
    private const KEY_YEAR_TO = 'yearTo';
    private const KEY_TIME_FROM = 'timeFrom';
    private const KEY_TIME_TO = 'timeTo';
    private const KEY_SORT_BY_QUERY = 'sortBy';
    private const KEY_SORT_BY_ARRAY = 'sortBy';
    private CONST ACCEPTED_SORT_FIELDS = ['id', 'title', 'year', 'time'];


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

    private function sortQueryToArray(?string $sortByQuery): ?array
    {
        if (null === $sortByQuery) {
            return null;
        }
        return array_intersect_key(array_reduce(
            explode(',', $sortByQuery),
            function ($carry, $item) {
                list($by, $order) = array_replace(
                    [1 => 'desc'],
                    explode(
                        ' ',
                        preg_replace('/\s+/', ' ', $item)
                    )
                );
                $carry[$by] = $order;

                return $carry;
            },
            []
        ), array_flip(self::ACCEPTED_SORT_FIELDS));
    }
}