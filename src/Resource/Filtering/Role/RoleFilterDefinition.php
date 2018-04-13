<?php

namespace App\Resource\Filtering\Role;

use App\Resource\Filtering\AbstractFilterDefinition;
use App\Resource\Filtering\FilterDefinitionInterface;

class RoleFilterDefinition extends AbstractFilterDefinition implements FilterDefinitionInterface
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
     * @return null|string
     */
    public function getSortBy(): ?string
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