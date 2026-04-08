<?php

namespace App\Models;

class Image {
    protected $id;
    protected $url;
    protected $alt;
    protected $is_cover;

    // get les properties
    public function getId(): ?int { return (int)$this->id ?: null; }
    public function getUrl(): string { return $this->url ?? ''; }
    public function getAlt(): string { return $this->alt ?? ''; }
    public function isCover(): bool { return (bool)$this->is_cover; }

    // set les properties
    public function setId(int $id): void { $this->id = $id; }
    public function setUrl(string $url): void { $this->url = $url; }
    public function setAlt(string $alt): void { $this->alt = $alt; }
    public function setIsCover(bool $is_cover): void { $this->is_cover = $is_cover; }
}
