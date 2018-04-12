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


    public function factory(Request $request): MovieFilterDefinition
    {
        return new MovieFilterDefinition(
            $request->get( self::KEY_TITLE),
            $request->get( self::KEY_YEAR_FROM),
            $request->get( self::KEY_YEAR_TO),
            $request->get( self::KEY_TIME_FROM),
            $request->get( self::KEY_TIME_TO)
        );
    }
}