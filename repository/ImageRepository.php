<?php

namespace App\Repository;

use App\Models\Image;
use App\Services\Database;
use PDO;

class ImageRepository extends AbstractRepository {

    // find image par son id
    public function find(int $id): ?Image {
        try {
            $stmt = $this->db->prepare("SELECT * FROM images WHERE id = ?");
            $stmt->execute([$id]);
            $image = $stmt->fetchObject(Image::class);
            return $image ?: null;
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    // find les images d'un project (galerie)
    public function findByProject(int $projectId): array {
        try {
            $stmt = $this->db->prepare("
                SELECT i.* FROM images i
                JOIN images_project ip ON i.id = ip.id_images
                WHERE ip.id_project = ?
            ");
            $stmt->execute([$projectId]);
            return $stmt->fetchAll(PDO::FETCH_CLASS, Image::class);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // save ou update l'image en db
    public function save(\App\Models\Image $image): bool {
        try {
            $is_cover = $image->isCover() ? 1 : 0;
            if ($image->getId()) {
                // execute l'update
                $stmt = $this->db->prepare("UPDATE images SET url = ?, alt = ?, is_cover = ? WHERE id = ?");
                return $stmt->execute([
                    $image->getUrl(),
                    $image->getAlt(),
                    $is_cover,
                    $image->getId()
                ]);
            } else {
                // insert la new row
                $stmt = $this->db->prepare("INSERT INTO images (url, alt, is_cover) VALUES (?, ?, ?)");
                $res = $stmt->execute([
                    $image->getUrl(),
                    $image->getAlt(),
                    $is_cover
                ]);
                if ($res) {
                    $image->setId((int)$this->db->lastInsertId());
                }
                return $res;
            }
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
