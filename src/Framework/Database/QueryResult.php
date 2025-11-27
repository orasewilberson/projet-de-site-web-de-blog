<?php
namespace Framework\Database;

use Framework\Database\Hydrator;

class QueryResult implements \ArrayAccess, \Iterator {

    private $records;

    private $index = 0;

    private $entity;

    private $hydratedRecords = [];

    public function __construct(array $records, ?string $entity = null)
    {
        $this->records = $records;
        $this->entity = $entity;
    }

    public function get(int $index) 
    {
        if($this->entity) {
            if(!isset($this->hydratedRecords[$index])) {
                $this->hydratedRecords[$index] = Hydrator::hydrate($this->records[$index], $this->entity);
            }
            return $this->hydratedRecords[$index];
        }
        return $this->entity;
    }

    /**
     * Generate by ArrayAcces
     *
     * @return void
     */
    public function current()
    {
      return $this->get($this->index); 
    }

    /**
     * Generate by ArrayAccess
     *
     * @return void
     */
    public function next()
    {
       $this->index++;
    }

    /**
     * Generate by iterator
     * @return void
     */
    public function key()
    {
       return $this->index;
    }

    /**
     * Generate by iterator
     *
     * @return void
     */
    public function valid()
    {
       return isset($this->records[$this->index]);
    }

    /**
     * Generate by iterator
     *
     * @return void
     */
    public function rewind()
    {
        $this->index = 0;
    }
    
    /**
     * Generate by ArrayAccess
     *
     * @param [type] $offset
     * @return void
     */
    public function offsetExists($offset)
    {
       return isset($this->records[$offset]);
    }

    /**
     * Generate by ArrayAccess
     *
     * @param [type] $offset
     * @return void
     */
    public function offsetGet($offset)
    {
       return $this->get($offset);
    }

    /**
     * Generate by ArrayAccess
     *
     * @param [type] $offset
     * @param [type] $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
       return new \Exception("can't alter records");
    }

    /**
     * Generate by ArrayAccess
     *
     * @param [type] $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        return new \Exception("can't alter records");
    }
}