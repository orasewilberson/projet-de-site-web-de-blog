<?php
namespace Framework\Database;

use IteratorAggregate;
use Pagerfanta\Pagerfanta;
use Framework\Database\Hydrator;
use Framework\Database\QueryResult;

class Query implements IteratorAggregate{
    
    private $select;

    private $from;

    private $entity;

    private $where = [];

    private $group;

    private $joins;

    private $order = [];

    private $limit;

    private $pdo;

    private $params = [];

    public function __construct(?\PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    public function from(string $table, ?string $alias = null): self
    {
        if($alias){
            $this->from[$table] = $alias;
        } else {
            $this->from[] = $table;
        }
        return $this;
    }

    public function select(string ...$fields): self
    {
        $this->select = $fields;
        return $this;
    }

    /**
     * permet de specifier la limite
     *
     * @param integer $length
     * @param integer $offset
     * @return self
     */
    public function limit(int $length, int $offset = 0): self
    {
        $this->limit = "$offset, $length";
        return $this;
    }

    /**
     * specifie l'ordre de recuperation
     * @param string $order
     * @return self
     */
    public function order(string $order): self
    {
        $this->order[] = $order;
        return $this;
    }

    /**
     * Ajout une liaison
     *
     * @param string $table
     * @param string $condition
     * @param string $type
     * @return Query
     */
    public function join(string $table, string $condition, string $type = "left"): self
    {
        $this->joins[$type][] = [$table, $condition];
        return $this;
    }

    /**
     * Definit la condition de recuperation
     *
     * @param string ...$conditions
     * @return Query
     */
    public function where(string ...$conditions): self
    {
        $this->where = array_merge($this->where, $conditions);
        return $this;
    }

    /**
     * Execute un COUNT() et renvoie la colonne
     *
     * @return integer
     */
    public function count(): int
    {
       $query = clone $this;
       $table = \current($this->from);
       return $query->select("COUNT($table.id)")->execute()->fetchColumn();
    }

    public function params(array $params): self
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    public function into(string $entity): self
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * Recupere un resultat
     *
     * @return void
     */
    public function fetch()
    {
       $record = $this->execute()->fetch(\PDO::FETCH_ASSOC);
        if($record === false) {
            return false;
        }
        if ($this->entity) {
            return Hydrator::hydrate($record, $this->entity);
        }
        return $record;
    }

    /**
     * Retournera un resultat ou renvoie une exception
     *
     * @return void
     */
    public function fetchOrFail()
    {
       $record = $this->fetch();
       if ($record === false) {
        throw new NoRecordException();
       }
       return $record;
    }

    /**
     * Lance la requete
     *
     * @return QueryResult
     */
    public function fetchAll(): QueryResult
    {
        return new QueryResult(
            $this->execute()->fetchAll(\PDO::FETCH_ASSOC)
        , $this->entity);
        
    }

    /**
     * pagine les resultats
     *
     * @param integer $perPage
     * @param integer $currentPage
     * @return Pagerfanta
     */
    public function paginate(int $perPage, int $currentPage = 1): Pagerfanta
    {
        $paginator = new PaginatedQuery($this);
        return (new PagerFanta($paginator))->setMaxPerPage($perPage)->setCurrentPage($currentPage);
    }
    
    public function __toString()
    {
        $parts = ['SELECT'];
        if($this->select) {
            $parts[] = join(', ', $this->select);
        } else {
            $parts[] = '*';
        }
        $parts[] = 'FROM';
        $parts[] = $this->buildFrom();
        if(!empty($this->joins)) {
            foreach ($this->joins as $type => $joins) {
                foreach ($joins as [$table, $condition]) {
                    $parts[] = \strtoupper($type) . " JOIN $table ON $condition";
                }
            }
        }
        if(!empty($this->where)){
            $parts[] = "WHERE";
            $parts[] ="(" . join( ') AND (', $this->where) . ')';
        }
        if (!empty($this->order)) {
            $parts[] = 'ORDER BY';
            $parts[] = join(', ', $this->order);
        }
        if ($this->limit) {
            $parts[] = 'LIMIT ' . $this->limit;
        }
        return join(' ', $parts);
        
    }

    private function buildFrom(): string
    {
        $from = [];
        foreach ($this->from as $key => $value) {
            if(is_string($key)) {
                $from[] = "$key as $value";
            } else {
                $from[] = $value;
            }
        }
        return join(', ', $from);
    }

    private function execute()
    {
        $query = $this->__toString();
        if(!empty($this->params)) {
            $statement = $this->pdo->prepare($query);
            $statement->execute($this->params);
            return $statement;
        }
        return $this->pdo->query($query);
    }

    public function getIterator()
    {
        return $this->fetchAll();
    }


}
