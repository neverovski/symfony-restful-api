<?php

namespace App\Resource\Filtering\Movie;

class MovieFilterDefinition
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
     * MovieFilterDefinition constructor.
     * @param null|string $title
     * @param int|null $yearFrom
     * @param int|null $yearTo
     * @param int|null $timeFrom
     * @param int|null $timeTo
     */
    public function __construct(
        ?string $title,
        ?int $yearFrom,
        ?int $yearTo,
        ?int $timeFrom,
        ?int $timeTo
    )
    {
        $this->title = $title;
        $this->yearFrom = $yearFrom;
        $this->yearTo = $yearTo;
        $this->timeFrom = $timeFrom;
        $this->timeTo = $timeTo;
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
     * @return array
     */
    public function getQueryParameters(): array
    {
        return get_object_vars($this);
    }
}