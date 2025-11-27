<?php
namespace Framework;

use Framework\Database\Table;
use Framework\Validator\ValidatorError;
use Psr\Http\Message\UploadedFileInterface;

class Validator {
    
    private const MINE_TYPES = [
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'pdf' => 'application/pdf'
    ];
    /**
     * @var array
     */
    private $params;

    /**
     * @var string[]
     */
    private $errors = [];

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * verifie que les champs sont presents dans le tableau
     * @param string ...$keys
     * @return self
     */
    public function required(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
           if(is_null($value)) {
            $this->addError($key, 'required');
           }
        }
        return $this;
    }

    /**
     * Verifie que le champs n'est pas vide
     * @param string ...$key
     * @return self
     */
    public function notEmpty(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if(is_null($value) || empty($value)) {
             $this->addError($key, 'empty');
            }
         }
         return $this;   
        }

    /**
     * verifie que l'element est un slug
     *
     * @param string $key
     * @return self
     */
    public function slug(string $key): self
    {
        $value = $this->getValue($key);
        if(is_null($value)){
            return $this;
        }
        $pattern = '/^[a-z0-9]+(-[a-z0-9]+)*$/';
        if(!is_null($value) && !preg_match($pattern, $value)) {
            $this->addError($key, 'slug');
        }
        return $this;
    }

    public function length(string $key, ?int $min, ?int $max = null): self
    {
        $value = $this->getValue($key);
        $length = \mb_strlen($value);
        if( !is_null($min) && !is_null($max) && 
        ($length < $min || $length > $max)) {
            $this->addError($key, 'betweenLength', [$min, $max]);
            return $this;
        }
        if( !is_null($min) && $length < $min) {
            $this->addError($key, 'minLength', [$min]);
            return $this;
        }
        if( !is_null($max) && $length > $max) {
            $this->addError($key, 'maxLength', [$max]);
            return $this;
        }
        return $this;
    }
    
    public function dateTime(string $key, string $format = "Y-m-d H:i:s"): self
    {
        $value = $this->getValue($key);
        $date = \DateTime::createFromFormat($format, $value);
        
        if ($date === false) {
            // Si la date n'a pas pu être créée, on ajoute une erreur.
            $this->addError($key, 'datetime', [$format]);
        } else {
            $errors = \DateTime::getLastErrors();
            // Vérifie si 'errors' est bien un tableau avant de tenter d'accéder à ses éléments.
            if (is_array($errors) && ($errors['error_count'] > 0 || $errors['warning_count'] > 0)) {
                $this->addError($key, 'datetime', [$format]);
            }
        }

        return $this;
    }

    /**
     * verifie que la clef exists dans la table donnee
     *
     * @param string $key
     * @param string $table
     * @param \PDO $pdo
     * @return self
     */
    public function exists(string $key, string $table, \PDO $pdo): self
    {
        $value = $this->getValue($key);
        $statement = $pdo->prepare("SELECT id FROM $table WHERE id = ?");
        $statement->execute([$value]);
        if($statement->fetchColumn() === false) {
            $this->addError($key, 'exists', [$table]);
        }
       return $this;
    }

    /**
     * verifie que la cle est unique dans la BD
     *
     * @param string $key
     * @param string $table
     * @param \PDO $pdo
     * @param integer|null $exclude
     * @return self
     */
    public function unique(string $key, string $table, \PDO $pdo, ?int $exclude = null): self
    {
        $value = $this->getValue($key);
        $query = "SELECT id FROM $table WHERE $key = ?";
        $params = [$value];
        if($exclude !== null){
            $query .= " AND id != ?";
            $params[] = $exclude;
        }
        $statement = $pdo->prepare($query);
        $statement->execute($params);
        if($statement->fetchColumn() !== false) {
            $this->addError($key, 'unique', [$value]);
        }
       return $this;
    }

    public function isValid(): bool
    {
        return empty($this->errors);
    }
    /**
     * Recupere les erreurs
     * @return ValidationError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * verifie si le fichier a bien ete uploader
     *
     * @param string $key
     * @return self
     */
    public function uploaded(string $key): self
    {
        $file = $this->getValue($key);
        if($file === null || $file->getError() !== UPLOAD_ERR_OK) {
            $this->addError($key, 'uploaded');
        }
        return $this;
    }
    /**
     * verifie le format de l'image
     *
     * @param string $key
     * @param array $extensions
     * @return self
     */
    public function extension(string $key, array $extensions): self
    {
        /** @var UploadedFileInterface */
        $file = $this->getValue($key);
        if($file !== null && $file->getError() === UPLOAD_ERR_OK) {
            $type = $file->getClientMediaType();
            $extension = \mb_strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
            $expectedType = self::MINE_TYPES[$extension] ?? null;
            if(!in_array($extension, $extensions) || $expectedType !== $type) {
                $this->addError($key, 'filetype', [join(',', $extensions)]);
            }
        }
        return $this;
    }

   /**
    * Ajout une erreur
    *
    * @param string $key
    * @param string $rule
    * @param array $attributes
    * @return void
    */
    private function addError(string $key, string $rule, array $attributes = []): void {
        $this->errors[$key] = new ValidatorError($key, $rule, $attributes);
    }

    public function getValue(string $key)
    {
        if(\array_key_exists($key, $this->params)){
            return $this->params[$key];
        }
        return null;
    }

    
}