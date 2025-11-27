<?php
namespace Framework\Validator;

class ValidatorError {

    private $key;

    private $rule;

    private $attributes;

    private $messages = [
        'required' => 'Le champs %s est requis',
        'empty' => 'Le champs %s ne peut etre vide',
        'slug' => 'Le champs %s n\'est pas un slug valide',
        'minLength' => 'Le champs %s doit contenir plus de %d caracteres',
        'maxLength' => 'Le champs %s doit contenir moins de %d caracteres',
        'betweenLength' => 'Le champs %s doit contenir entre %d et %d caracteres',
        'maxLength' => 'Le champs %s doit etre une date valide (%s)',
        'exists' => 'Le champs %s n\'existe pas dans la table %s',
        'unique' => 'Le champs %s doit etre unique',
        'filetype' => 'Le champs %s n\'est pas au bon format (%s)',
        'uploaded' => 'Vous devez uploader un fichier'
    ];

    public function __construct(string $key, string $rule, array $attributes = [])
    {
        $this->key = $key;
        $this->rule = $rule;
        $this->attributes = $attributes;
    }

    public function __toString()
    {
        $params = array_merge([$this->messages[$this->rule], $this->key], $this->attributes);
        return (string)call_user_func_array('sprintf', $params);
    }
}