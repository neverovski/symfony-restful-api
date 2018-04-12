<?php

namespace App\Resource\Pagination;


class Page
{
    /**
     * @var int
     */
    private $page;
    /**
     * @var int
     */
    private $limit;

    /**
     * @var float|int
     */
    private $offset;

    /**
     * Page constructor.
     * @param int $page
     * @param int $limit
     */
    public function __construct(int $page, int $limit)
    {
        $this->page = $page;
        $this->limit = $limit;
        $this->offset = ($page - 1) * $limit;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return float|int
     */
    public function getOffset()
    {
        return $this->offset;
    }


}