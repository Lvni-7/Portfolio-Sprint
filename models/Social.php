<?php

namespace App\Models;

class Social {
    protected $id;
    protected $name;
    protected $icon;
    protected $url;

    // get les properties
    public function getId(): ?int { return (int)$this->id ?: null; }
    public function getName(): string { return $this->name ?? ''; }
    public function getIcon(): string { return $this->icon ?? ''; }
    public function getUrl(): string { return $this->url ?? ''; }

    // set les properties
    public function setId(int $id): void { $this->id = $id; }
    public function setName(string $name): void { $this->name = $name; }
    public function setIcon(string $icon): void { $this->icon = $icon; }
    public function setUrl(string $url): void { $this->url = $url; }
}
