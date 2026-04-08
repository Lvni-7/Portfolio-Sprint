<?php

namespace App\Repository;

use App\Services\Database;

abstract class AbstractRepository {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }
}
