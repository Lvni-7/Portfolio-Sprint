<?php

namespace App\Repository;

use App\Models\Skill;
use App\Services\Database;
use PDO;

class SkillRepository extends AbstractRepository {

    // fetch all skills sort par name
    public function findAll(): array {
        try {
            $stmt = $this->db->query("SELECT * FROM skills ORDER BY name ASC");
            return $stmt->fetchAll(PDO::FETCH_CLASS, Skill::class);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // find un skill par son id
    public function find(int $id): ?Skill {
        try {
            $stmt = $this->db->prepare("SELECT * FROM skills WHERE id = ?");
            $stmt->execute([$id]);
            $skill = $stmt->fetchObject(Skill::class);
            return $skill ?: null;
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    // find les skills liés à un project
    public function findByProject(int $projectId): array {
        try {
            $stmt = $this->db->prepare("
                SELECT s.* FROM skills s
                JOIN projects_skills ps ON s.id = ps.id_skill
                WHERE ps.id_project = ?
            ");
            $stmt->execute([$projectId]);
            return $stmt->fetchAll(PDO::FETCH_CLASS, Skill::class);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // count le total de skills
    public function countAll(): int {
        return count($this->findAll());
    }

    // save ou update le skill
    public function save(Skill $skill): bool {
        try {
            if ($skill->getId()) {
                // update existant
                $stmt = $this->db->prepare("UPDATE skills SET name = ?, level = ? WHERE id = ?");
                return $stmt->execute([
                    $skill->getName(),
                    $skill->getLevel(),
                    $skill->getId()
                ]);
            } else {
                // insert new row
                $stmt = $this->db->prepare("INSERT INTO skills (name, level) VALUES (?, ?)");
                $res = $stmt->execute([
                    $skill->getName(),
                    $skill->getLevel()
                ]);
                if ($res) {
                    $skill->setId((int)$this->db->lastInsertId());
                }
                return $res;
            }
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // delete le skill de la table
    public function delete(int $id): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM skills WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
