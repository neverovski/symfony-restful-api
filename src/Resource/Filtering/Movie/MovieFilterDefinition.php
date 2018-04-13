<?php

namespace App\Resource\Filtering\Movie;

use App\Resource\Filtering\AbstractFilterDefinition;
use App\Resource\Filtering\FilterDefinitionInterface;
use App\Resource\Filtering\SortTableFilterDefinitionInterface;

class MovieFilterDefinition
    extends AbstractFilterDefinition
    implements FilterDefinitionInterface, SortTableFilterDefinitionInterface
{
    /**
     * @var null|string
     */
    private $title;

    /**
     * @var int|null
     */
    private $yearFrom;

    /**
     * @var int|null
     */
    private $yearTo;

    /**
     * @var int|null
     */
    private $timeFrom;

    /**
     * @var int|null
     */
    private $timeTo;

    /**
     * @var array|null
     */
    private $sortByArray;

    /**
     * @var null|string
     */
    private $sortBy;

    /**
     * MovieFilterDefinition constructor.
     * @param null|string $title
     * @param int|null $yearFrom
     * @param int|null $yearTo
     * @param int|null $timeFrom
     * @param int|null $timeTo
     * @param null|string $sortByQuery
     * @param array|null $sortByArray
     */
    public function __construct(
        ?string $title,
        ?int $yearFrom,
        ?int $yearTo,
        ?int $timeFrom,
        ?int $timeTo,
        ?string $sortByQuery,
        ?array $sortByArray
    )
    {
        $this->title = $title;
        $this->yearFrom = $yearFrom;
        $this->yearTo = $yearTo;
        $this->timeFrom = $timeFrom;
        $this->timeTo = $timeTo;
        $this->sortBy = $sortByQuery;
        $this->sortByArray = $sortByArray;
    }

    /**
     * @return null|string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return int|null
     */
    public function getYearFrom(): ?int
    {
        return $this->yearFrom;
    }

    /**
     * @return int|null
     */
    public function getYearTo(): ?int
    {
        return $this->yearTo;
    }

    /**
     * @return int|null
     */
    public function getTimeFrom(): ?int
    {
        return $this->timeFrom;
    }

    /**
     * @return int|null
     */
    public function getTimeTo(): ?int
    {
        return $this->timeTo;
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