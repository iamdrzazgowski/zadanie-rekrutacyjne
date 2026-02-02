<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Contact
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 20)]
    private ?string $phone = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $email = null;

    #[ORM\OneToOne(inversedBy: "contact")]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // ===== Gettery i Settery =====
    public function getId(): ?int { return $this->id; }
    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $phone): self { $this->phone = $phone; return $this; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): self { $this->email = $email; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(User $user): self { $this->user = $user; return $this; }
}
