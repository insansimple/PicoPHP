<?php

namespace App\Models;

use System\Core\Model;
use System\Library\Database;

class User extends Model
{
    protected $table = 'users';

    public function listData()
    {
        $db = Database::getInstance();
        $data = $db->query("SELECT * FROM {$this->table}")->resultSet();
        return $data;
    }
}
