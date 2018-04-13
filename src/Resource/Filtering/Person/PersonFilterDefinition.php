<?php

namespace App\Resource\Filtering\Person;

use App\Resource\Filtering\AbstractFilterDefinition;
use App\Resource\Filtering\FilterDefinitionInterface;
use App\Resource\Filtering\SortTableFilterDefinitionInterface;

class PersonFilterDefinition
    extends AbstractFilterDefinition
    implements FilterDefinitionInterface, SortTableFilterDefinitionInterface
{
    /**
     * @var null|string
     */
    private $firstName;

    /**
     * @var int|null
     */
    private $lastName;

    /**
     * @var int|null
     */
    private $birthFrom;

    /**
     * @var int|null
     */
    private $birthTo;

    /**
     * @var array|null
     */
    private $sortByArray;

    /**
     * @var null|string
     */
    private $sortBy;

    /**
     * PersonFilterDefinition constructor.
     * @param null|string $firstName
     * @param int|null $lastName
     * @param int|null $birthFrom
     * @param int|null $birthTo
     * @param null|string $sortByQuery
     * @param array|null $sortByArray
     */
    public function __construct(
        ?string $firstName,
        ?int $lastName,
        ?int $birthFrom,
        ?int $birthTo,
        ?string $sortByQuery,
        ?array $sortByArray
    )
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->birthFrom = $birthFrom;
        $this->birthTo = $birthTo;
        $this->sortBy = $sortByQuery;
        $this->sortByArray = $sortByArray;
    }

    /**
     * @return null|string
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @return int|null
     */
    public function getLastName(): ?int
    {
        return $this->lastName;
    }

    /**
     * @return int|null
     */
    public function getBirthFrom(): ?int
    {
        return $this->birthFrom;
    }

    /**
     * @return int|null
     */
    public function getBirthTo(): ?int
    {
        return $this->birthTo;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return get_object_vars($this);
    }

    /**
     * @return array|null
     */
    public function getSortByArray(): ?array
    {
        return $this->sortByArray;
    }

    /**
     * @return null|string
     */
    public function getSortByQuery(): ?string
    {
        return $this->sortBy;
    }
}