<?php

namespace App\Resource\Filtering\Person;

use App\Resource\Filtering\AbstractFilterDefinitionFactory;
use App\Resource\Filtering\FilterDefinitionFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class PersonFilterDefinitionFactory
    extends AbstractFilterDefinitionFactory
    implements FilterDefinitionFactoryInterface
{
    private const KEY_FIRST_NAME = 'firstName';
    private const KEY_LAST_NAME = 'lastName';
    private const KEY_BIRTH_FROM = 'birthFrom';
    private const KEY_BIRTH_TO = 'birthTo';
    private const KEY_SORT_BY_QUERY = 'sortBy';
    private const KEY_SORT_BY_ARRAY = 'sortBy';
    private CONST ACCEPTED_SORT_FIELDS = ['id', 'firstName', 'lastName', 'dateOfBirth'];

    /**
     * @param Request $request
     * @return PersonFilterDefinition
     */
    public function factory(Request $request): PersonFilterDefinition
    {
        return new PersonFilterDefinition(
            $request->get(self::KEY_FIRST_NAME),
            $request->get(self::KEY_LAST_NAME),
            $request->get(self::KEY_BIRTH_FROM),
            $request->get(self::KEY_BIRTH_TO),
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