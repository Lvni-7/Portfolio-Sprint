<?php

namespace App\Repository;

use App\Models\Admin;
use App\Services\Database;
use PDO;

class AdminRepository extends AbstractRepository {

    // find admin par son id
    public function find(int $id): ?Admin {
        try {
            $stmt = $this->db->prepare("SELECT * FROM admin WHERE id = ?");
            $stmt->execute([$id]);
            $admin = $stmt->fetchObject(Admin::class);
            return $admin ?: null;
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    // find admin par son email (pour le login)
    public function findByEmail(string $email): ?Admin {
        try {
            $stmt = $this->db->prepare("SELECT * FROM admin WHERE email = ?");
            $stmt->execute([$email]);
            $admin = $stmt->fetchObject(Admin::class);
            return $admin ?: null;
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }
}
