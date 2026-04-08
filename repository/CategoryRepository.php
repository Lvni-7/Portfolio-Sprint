<?php

namespace App\Repository;

use App\Models\Category;
use App\Services\Database;
use PDO;

class CategoryRepository extends AbstractRepository {

    // fetch all categories sort par name
    public function findAll(): array {
        try {
            $stmt = $this->db->query("SELECT * FROM category ORDER BY name ASC");
            return $stmt->fetchAll(PDO::FETCH_CLASS, Category::class);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // find category par son id
    public function find(int $id): ?Category {
        try {
            $stmt = $this->db->prepare("SELECT * FROM category WHERE id = ?");
            $stmt->execute([$id]);
            $category = $stmt->fetchObject(Category::class);
            return $category ?: null;
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    // save ou update la category en db
    public function save(Category $category): bool {
        try {
            if ($category->getId()) {
                // update
                $stmt = $this->db->prepare("UPDATE category SET name = ? WHERE id = ?");
                return $stmt->execute([
                    $category->getName(),
                    $category->getId()
                ]);
            } else {
                // insert new row
                $stmt = $this->db->prepare("INSERT INTO category (name) VALUES (?)");
                $res = $stmt->execute([
                    $category->getName()
                ]);
                if ($res) {
                    $category->setId((int)$this->db->lastInsertId());
                }
                return $res;
            }
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // delete row de la table category
    public function delete(int $id): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM category WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // count total rows
    public function countAll(): int {
        try {
            return (int)$this->db->query("SELECT COUNT(*) FROM category")->fetchColumn();
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return 0;
        }
    }
}
