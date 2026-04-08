<?php

namespace App\Models;

use App\Repository\CategoryRepository;
use App\Repository\SkillRepository;
use App\Repository\ImageRepository;

class Project {
    protected $id;
    protected $title;
    protected $description;
    protected $image;
    protected $created_at;
    protected $id_category;

    // get les properties
    public function getId(): ?int { return (int)$this->id ?: null; }
    public function getTitle(): string { return $this->title ?? ''; }
    public function getDescription(): string { return $this->description ?? ''; }
    public function getImage(): string { return $this->image ?? ''; }
    public function getCreatedAt(): ?string { return $this->created_at; }
    public function getIdCategory(): ?int { return (int)$this->id_category ?: null; }

    // set les properties
    public function setId(int $id): void { $this->id = $id; }
    public function setTitle(string $title): void { $this->title = $title; }
    public function setDescription(string $description): void { $this->description = $description; }
    public function setImage(string $image): void { $this->image = $image; }
    public function setIdCategory(int $id_category): void { $this->id_category = $id_category; }

    // logic de relations
    public function getCategory(): ?Category {
        if (!$this->id_category) {
            return null;
        }
        $categoryRepo = new CategoryRepository();
        return $categoryRepo->find((int)$this->id_category);
    }

    public function getSkills(): array {
        $skillRepo = new SkillRepository();
        return $skillRepo->findByProject($this->id);
    }

    public function getImages(): array {
        $imageRepo = new ImageRepository();
        return $imageRepo->findByProject($this->id);
    }
}
