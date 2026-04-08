<?php

namespace App\Models;

class Admin {
    protected $id;
    protected $email;
    protected $password;

    // get les values
    public function getId(): ?int { return (int)$this->id ?: null; }
    public function getEmail(): string { return $this->email ?? ''; }
    public function getPassword(): string { return $this->password ?? ''; }

    // set les values
    public function setId(int $id): void { $this->id = $id; }
    public function setEmail(string $email): void { $this->email = $email; }
    public function setPassword(string $password): void { $this->password = $password; }
}
