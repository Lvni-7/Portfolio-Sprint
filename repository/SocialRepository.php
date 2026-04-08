<?php

namespace App\Repository;

use App\Models\Social;
use App\Services\Database;
use PDO;

class SocialRepository extends AbstractRepository {

    // fetch all socials links
    public function findAll(): array {
        try {
            $stmt = $this->db->query("SELECT * FROM socials ORDER BY name ASC");
            return $stmt->fetchAll(PDO::FETCH_CLASS, Social::class);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // find un social link par son id
    public function find(int $id): ?Social {
        try {
            $stmt = $this->db->prepare("SELECT * FROM socials WHERE id = ?");
            $stmt->execute([$id]);
            $social = $stmt->fetchObject(Social::class);
            return $social ?: null;
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    // count total links sociales
    public function countAll(): int {
        try {
            return (int)$this->db->query("SELECT COUNT(*) FROM socials")->fetchColumn();
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return 0;
        }
    }

    // save ou update le social link
    public function save(Social $social): bool {
        try {
            if ($social->getId()) {
                // update
                $stmt = $this->db->prepare("UPDATE socials SET name = ?, icon = ?, url = ? WHERE id = ?");
                return $stmt->execute([
                    $social->getName(),
                    $social->getIcon(),
                    $social->getUrl(),
                    $social->getId()
                ]);
            } else {
                // insert new row
                $stmt = $this->db->prepare("INSERT INTO socials (name, icon, url) VALUES (?, ?, ?)");
                $res = $stmt->execute([
                    $social->getName(),
                    $social->getIcon(),
                    $social->getUrl()
                ]);
                if ($res) {
                    $social->setId((int)$this->db->lastInsertId());
                }
                return $res;
            }
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // delete row de la table socials
    public function delete(int $id): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM socials WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
