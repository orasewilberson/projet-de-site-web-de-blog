<?php
namespace App\Blog\Session;


interface SessionInterface {

    /**
     * Recupere une information en session
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Ajoute une information en session
     * @param string $key
     * @param [type] $value
     * @return void
     */
    public function set(string $key, $value): void;


    /**
     * supprime une clef en session
     * @param string $key
     * @return void
     */
    public function delete(string $key): void;
}