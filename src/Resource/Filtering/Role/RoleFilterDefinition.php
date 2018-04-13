<?php

namespace App\Resource\Filtering\Role;

use App\Resource\Filtering\AbstractFilterDefinition;
use App\Resource\Filtering\FilterDefinitionInterface;
use App\Resource\Filtering\SortTableFilterDefinitionInterface;

class RoleFilterDefinition
    extends AbstractFilterDefinition
    implements FilterDefinitionInterface, SortTableFilterDefinitionInterface
{
    /**
     * @var null|string
     */
    private $playedName;

    /**
     * @var null|string
     */
    private $sortBy;

    /**
     * @var array|null
     */
    private $sortByArray;

    /**
     * @var int|null
     */
    private $movie;

    /**
     * RoleFilterDefinition constructor.
     * @param null|string $playedName
     * @param int|null $movie
     * @param null|string $sortByQuery
     * @param array|null $sortByArray
     */
    public function __construct(
        ?string $playedName,
        ?int $movie,
        ?string $sortByQuery,
        ?array $sortByArray
    )
    {
        $this->playedName = $playedName;
        $this->movie = $movie;
        $this->sortBy = $sortByQuery;
        $this->sortByArray = $sortByArray;
    }

    /**
     * @return null|string
     */
    public function getPlayedName(): ?string
    {
        return $this->playedName;
    }


    /**
     * @return int|null
     */
    public function getMovie(): ?int
    {
        return $this->movie;
    }

    /**
     * @return null|string
     */
    public function getSortByQuery(): ?string
    {
        return $this->sortBy;
    }

    /**
     * @return array|null
     */
    public function getSortByArray(): ?array
    {
        return $this->sortByArray;
    }
}