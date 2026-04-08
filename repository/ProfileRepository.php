<?php

namespace App\Repository;

use App\Models\Profile;
use App\Services\Database;
use PDO;

class ProfileRepository extends AbstractRepository {

    // fetch le seul profil du site
    public function findOne(): ?Profile {
        try {
            // limit 1 car 1 seul profile possible
            $stmt = $this->db->query("SELECT * FROM profile LIMIT 1");
            $profile = $stmt->fetchObject(Profile::class);
            return $profile ?: null;
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    // find profile par son id
    public function find(int $id): ?Profile {
        try {
            $stmt = $this->db->prepare("SELECT * FROM profile WHERE id = ?");
            $stmt->execute([$id]);
            $profile = $stmt->fetchObject(Profile::class);
            return $profile ?: null;
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    // save ou update les infos du profil
    public function save(Profile $profile): bool {
        try {
            if ($profile->getId()) {
                // execute l'update
                $stmt = $this->db->prepare("UPDATE profile SET name = ?, description = ?, date_of_birth = ?, email = ?, phone_number = ?, id_image = ? WHERE id = ?");
                return $stmt->execute([
                    $profile->getName(),
                    $profile->getDescription(),
                    $profile->getDateOfBirth(),
                    $profile->getEmail(),
                    $profile->getPhoneNumber(),
                    $profile->getIdImage(),
                    $profile->getId()
                ]);
            } else {
                // insert le new profile
                $stmt = $this->db->prepare("INSERT INTO profile (name, description, date_of_birth, email, phone_number, id_image) VALUES (?, ?, ?, ?, ?, ?)");
                $res = $stmt->execute([
                    $profile->getName(),
                    $profile->getDescription(),
                    $profile->getDateOfBirth(),
                    $profile->getEmail(),
                    $profile->getPhoneNumber(),
                    $profile->getIdImage()
                ]);
                if ($res) {
                    $profile->setId((int)$this->db->lastInsertId());
                }
                return $res;
            }
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
