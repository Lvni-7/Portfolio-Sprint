<?php

namespace App\Repository;

use App\Models\Project;
use App\Services\Database;
use PDO;

class ProjectRepository extends AbstractRepository {

    // fetch all projects de la db
    public function findAll(): array {
        try {
            $stmt = $this->db->query("SELECT * FROM project ORDER BY created_at DESC");
            return $stmt->fetchAll(PDO::FETCH_CLASS, Project::class);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // find projects d'une categorie
    public function findByCategory(int $categoryId): array {
        try {
            $stmt = $this->db->prepare("SELECT * FROM project WHERE id_category = ?");
            $stmt->execute([$categoryId]);
            return $stmt->fetchAll(PDO::FETCH_CLASS, Project::class);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // find un project par son id
    public function find(int $id): ?Project {
        try {
            $stmt = $this->db->prepare("SELECT * FROM project WHERE id = ?");
            $stmt->execute([$id]);
            $project = $stmt->fetchObject(Project::class);
            return $project ?: null;
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    // save ou update le project
    public function save(Project $project): bool {
        try {
            if ($project->getId()) {
                // update existant
                $stmt = $this->db->prepare("UPDATE project SET title = ?, description = ?, image = ?, id_category = ? WHERE id = ?");
                return $stmt->execute([
                    $project->getTitle(),
                    $project->getDescription(),
                    $project->getImage(),
                    $project->getIdCategory(),
                    $project->getId()
                ]);
            } else {
                // insert new row
                $stmt = $this->db->prepare("INSERT INTO project (title, description, image, id_category) VALUES (?, ?, ?, ?)");
                $res = $stmt->execute([
                    $project->getTitle(),
                    $project->getDescription(),
                    $project->getImage(),
                    $project->getIdCategory()
                ]);
                if ($res) {
                    $project->setId((int)$this->db->lastInsertId());
                }
                return $res;
            }
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // delete le project de la db
    public function delete(int $id): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM project WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // count le total de projects
    public function countAll(): int {
        try {
            return (int)$this->db->query("SELECT COUNT(*) FROM project")->fetchColumn();
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return 0;
        }
    }

    // get les skills liés au project
    public function findSkills(int $projectId): array {
        try {
            $stmt = $this->db->prepare("SELECT id_skill FROM projects_skills WHERE id_project = ?");
            $stmt->execute([$projectId]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // sync les skills (delete et re-insert)
    public function syncSkills(int $projectId, array $skillIds): void {
        try {
            // clean les anciens links
            $stmt = $this->db->prepare("DELETE FROM projects_skills WHERE id_project = ?");
            $stmt->execute([$projectId]);

            // add les nouveaux links
            if (!empty($skillIds)) {
                $sql = "INSERT INTO projects_skills (id_project, id_skill) VALUES ";
                $values = [];
                $placeholders = [];
                foreach ($skillIds as $skillId) {
                    $placeholders[] = "(?, ?)";
                    $values[] = $projectId;
                    $values[] = $skillId;
                }
                $sql .= implode(", ", $placeholders);
                $stmt = $this->db->prepare($sql);
                $stmt->execute($values);
            }
        } catch (\PDOException $e) {
            error_log($e->getMessage());
        }
    }

    // sync les images de la galerie (urls)
    public function syncImages(int $projectId, array $imageUrls): void {
        try {
            // clean les liens existants dans images_project
            $stmt = $this->db->prepare("DELETE FROM images_project WHERE id_project = ?");
            $stmt->execute([$projectId]);

            // add les nouvelles images
            foreach ($imageUrls as $url) {
                if (empty(trim($url))) continue;

                $url = trim($url);

                // check si l'image existe deja (par son url) ou en creer une new
                $stmt = $this->db->prepare("SELECT id FROM images WHERE url = ?");
                $stmt->execute([$url]);
                $imageId = $stmt->fetchColumn();

                if (!$imageId) {
                    $stmt = $this->db->prepare("INSERT INTO images (url, alt) VALUES (?, ?)");
                    $stmt->execute([$url, "Image du projet " . $projectId]);
                    $imageId = $this->db->lastInsertId();
                }

                // lier le project a l'image
                $stmt = $this->db->prepare("INSERT INTO images_project (id_project, id_images) VALUES (?, ?)");
                $stmt->execute([$projectId, $imageId]);
            }
        } catch (\PDOException $e) {
            error_log($e->getMessage());
        }
    }
}
