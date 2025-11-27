<?php
namespace Framework\Database;

use PDO;
use Framework\Database\Query;
use Framework\Database\QueryResult;
use Pagerfanta\Adapter\AdapterInterface;

class PaginatedQuery implements AdapterInterface {

    /**
     * @var Query
     */
    private $query;

    /**
     * @param Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * Returns the number of results
     *
     * @return integer the number of results
     */
    public function getNbResults(): int
    {
        return $this->query->count();
    }

       /**
     * Returns a slice of the results representing the current page of items in the list.
     *
     * @phpstan-param int<0, max> $offset
     * @phpstan-param int<0, max> $length
     *
     * @return iterable<array-key, T>
     */
    public function getSlice(int $offset, int $length): QueryResult
    {
        $query = clone $this->query;
        return $query->limit($length, $offset)->fetchAll();
    }
}