<?php

namespace App\Models;

class Skill {
    protected $id;
    protected $name;
    protected $level;

    // get les properties
    public function getId(): ?int { return (int)$this->id ?: null; }
    public function getName(): string { return $this->name ?? ''; }
    public function getLevel(): int { return (int)$this->level; }

    // set les properties
    public function setId(int $id): void { $this->id = $id; }
    public function setName(string $name): void { $this->name = $name; }
    public function setLevel(int $level): void { $this->level = $level; }
}
