<?php

namespace App\Models;

use App\Repository\ProjectRepository;

class Category {
    protected $id;
    protected $name;

    // get les values
    public function getId(): ?int { return (int)$this->id ?: null; }
    public function getName(): string { return $this->name ?? ''; }

    // set les values
    public function setId(int $id): void { $this->id = $id; }
    public function setName(string $name): void { $this->name = $name; }

    // logic de relations
    public function getProjects(): array {
        $projectRepo = new ProjectRepository();
        return $projectRepo->findByCategory($this->id);
    }
}
