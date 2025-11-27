<?php
namespace Framework;

use App\Auth\UserEntity;

interface Auth {

    /**
     * @return UserEntity|null
     */
    public function getUser(): ?UserEntity;
}