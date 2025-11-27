<?php
namespace Framework\Database;

use PDO;
use Pagerfanta\Pagerfanta;
use Framework\Database\Query;
use Framework\Database\PaginatedQuery;
use Framework\Database\NoRecordException;

class Table {
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * Nom de la table en BDD
     * @var string
     */
    protected $table;

    /**
     * Entite a utiliser
     * @var string|null
     */
    protected $entity = \stdClass::class;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

 
    /**
     * Reupere une liste clef valeur de nos enregistrements
     * @return array
     */
    public function findList(): array
    {
        $results = $this->pdo
            ->query("SELECT id, name FROM {$this->table}")
            ->fetchAll(PDO::FETCH_NUM);
            $list = [];
            foreach ($results as $result) {
                $list[$result[0]] = $result[1];
            }
            return $list;
    }

    /**
     * @return Query
     */
    public function makeQuery(): Query
    {
        return (new Query($this->pdo))
            ->from($this->table, $this->table[0])
            ->into($this->entity);
    }

    /**
     * Recupere tous les enregistrements 
     * @return Query
     */
    public function findAll(): Query
    {
        return $this->makeQuery();
    }

    /**
     * Recupere une ligne par rapport a un champs
     *
     * @param string $field
     * @param string $value
     * @return array
     * @throws NoRecordException
     */
    public function findBy(string $field, string $value)
    {
        return $this->makeQuery()->where("$field = :field")->params(["field" => $value])->fetchOrFail();
       
    }

    /**
     * Recuperer un element a partir de son id
     * @param integer $id
     * @return mixed
     */
    public function find(int $id) 
    {
        return $this->makeQuery()->where("id = $id")->fetchOrFail();
    }

    /**
     * Recupere le nbre d'enregistrement
     *
     * @return integer
     */
    public function count(): int
    {
        return $this->makeQuery()->count();
    }

    /**
     * Met a jour un enregistrement au niveau de la base de donnees
     * @param integer $id
     * @param array $params
     * @return boolean
     */
    public function update(int $id, array $params): bool
    {
        $fieldQuery = $this->buildFieldQuery($params);
        $params["id"] = $id;
        $statement = $this->pdo->prepare("UPDATE {$this->table} SET $fieldQuery WHERE id = :id");
        return $statement->execute($params);
    }

    /**
     * Creer un enregistrement
     * @param array $params
     * @return boolean
     */
    public function insert(array $params): bool
    {
        $fields = array_keys($params);
        $values = join(', ', array_map(function ($field) {
            return ':' . $field;
        }, $fields));
        $fields = join(', ', $fields);
        $statement = $this->pdo->prepare("INSERT INTO {$this->table} ($fields) VALUES ($values)");
        return $statement->execute($params);
    }

    /**
     * Supprime un enregistrement
     * @param integer $id
     * @return boolean
     */
    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $statement->execute([$id]);
    }

    private function buildFieldQuery(array $params): string {
        return join(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
    }

    /**
     * Get nom de la table en BDD
     *
     * @return  string
     */ 
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Get entite a utiliser
     *
     * @return  string
     */ 
    public function getEntity(): string
    {
        return $this->entity;
    }

    /**
     * Get the value of pdo
     *
     * @return  \PDO
     */ 
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * verifie qu'un enregistrement existe
     *
     * @param [type] $id
     * @return boolean
     */
    public function exists($id): bool
    {
        $statement = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE id = ?");
        $statement->execute([$id]);
        return $statement->fetchColumn() !== false;
    }


}
