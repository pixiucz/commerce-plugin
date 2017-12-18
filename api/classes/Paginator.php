<?php namespace Pixiu\Commerce\api\Classes;


class Paginator
{
    private $limit;
    private $offset;
    private $orderBy;
    private $orderDir;

    /**
     * Paginator constructor.
     * @param $limit
     * @param $offset
     * @param $orderBy
     * @param $orderDir
     */
    public function __construct(
        int $limit = 25,
        int $offset = 0,
        string $orderBy = 'id',
        string $orderDir = 'DESC'
    )
    {
        $this->limit = $limit;
        $this->offset = $offset;
        $this->orderBy = $orderBy;
        $this->orderDir = $orderDir;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset(int $offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return string
     */
    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    /**
     * @param string $orderBy
     */
    public function setOrderBy(string $orderBy)
    {
        $this->orderBy = $orderBy;
    }

    /**
     * @return string
     */
    public function getOrderDir(): string
    {
        return $this->orderDir;
    }

    /**
     * @param string $orderDir
     */
    public function setOrderDir(string $orderDir)
    {
        $this->orderDir = $orderDir;
    }
}