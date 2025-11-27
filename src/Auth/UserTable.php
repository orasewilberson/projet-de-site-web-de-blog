<?php
namespace App\Auth;

use App\Auth\UserEntity;
use Framework\Database\Table;

class UserTable extends Table{
    
    protected $table = "users";

    protected $entity = UserEntity::class;
}