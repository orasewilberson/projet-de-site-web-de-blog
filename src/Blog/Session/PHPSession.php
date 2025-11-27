<?php
namespace App\Blog\Session;




class PHPSession implements SessionInterface, \ArrayAccess
{  

    private function ensureStarted(){
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }
    }
    /**
     * Recupere une information en session
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $this->ensureStarted();
        if(array_key_exists($key, $_SESSION)){
            return $_SESSION[$key];
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
        $this->ensureStarted();
        $_SESSION[$key] = $value;
    }


    /**
     * supprime une clef en session
     * @param string $key
     * @return void
     */
    public function delete(string $key): void
    {
        $this->ensureStarted();
        unset($_SESSION[$key]);
    }

    /**
     * Methode generer par ArrayAcess
     *
     * @param [type] $offset
     * @return mixed
     */
    public function offsetExists($offset)
    {
        $this->ensureStarted();
        return \array_key_exists($offset, $_SESSION);
    }

    /**
     * Methode generer par ArrayAcess
     *
     * @param [type] $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

     /**
      * Methode generer par ArrayAcess
      *
      * @param [type] $offset
      * @param [type] $value
      * @return void
      */
    public function offsetset($offset, $value)
    {
        return $this->set($offset, $value);
    }

      /**
      * Methode generer par ArrayAcess
      *
      * @param [type] $offset
      * @return void
      */
      public function offsetUnset($offset)
      {
         $this->delete($offset);
      }
}