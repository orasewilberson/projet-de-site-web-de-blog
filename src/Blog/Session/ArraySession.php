<?php
namespace App\Blog\Session;



class ArraySession implements SessionInterface
{  

    private $session = [];

    /**
     * Recupere une information en session
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        if(array_key_exists($key, $this->session)){
            return $this->session[$key];
        }
        return $default;
    }

    /**
     * Ajoute une information en session
     * @param string $key
     * @param [type] $value
     * @return void
     */
    public function set(string $key, $value): void
    {
        $this->session[$key] = $value;
    }


    /**
     * supprime une clef en session
     * @param string $key
     * @return void
     */
    public function delete(string $key): void
    {
        unset($this->session[$key]);
    }
}