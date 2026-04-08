<?php

namespace App\Models;

use App\Repository\ImageRepository;

class Profile {
    protected $id;
    protected $name;
    protected $description;
    protected $date_of_birth;
    protected $email;
    protected $phone_number;
    protected $id_image;

    // get les properties
    public function getId(): ?int { return (int)$this->id ?: null; }
    public function getName(): string { return $this->name ?? ''; }
    public function getDescription(): string { return $this->description ?? ''; }
    public function getDateOfBirth(): string { return $this->date_of_birth ?? ''; }
    public function getEmail(): string { return $this->email ?? ''; }
    public function getPhoneNumber(): string { return $this->phone_number ?? ''; }
    public function getIdImage(): ?int { return (int)$this->id_image ?: null; }

    // set les properties
    public function setId(int $id): void { $this->id = $id; }
    public function setName(string $name): void { $this->name = $name; }
    public function setDescription(string $description): void { $this->description = $description; }
    public function setDateOfBirth(string $date_of_birth): void { $this->date_of_birth = $date_of_birth; }
    public function setEmail(string $email): void { $this->email = $email; }
    public function setPhoneNumber(string $phone_number): void { $this->phone_number = $phone_number; }
    public function setIdImage(int $id_image): void { $this->id_image = $id_image; }
    
    // logic de relations
    public function getImage(): ?Image {
        if (!$this->id_image) {
            return null;
        }
        $imageRepo = new ImageRepository();
        return $imageRepo->find((int)$this->id_image);
    }
}
