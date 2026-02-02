<?php
// src/Entity/Experience.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Experience
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:255)]
    #[Assert\NotBlank]
    private ?string $company = null;

    #[ORM\Column(type:"string", length:255)]
    #[Assert\NotBlank]
    private ?string $position = null;

    #[ORM\Column(type:"date")]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $dateFrom = null;

    #[ORM\Column(type:"date")]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $dateTo = null;

    #[ORM\ManyToOne(targetEntity:User::class, inversedBy:"experiences")]
    #[ORM\JoinColumn(nullable:false)]
    private ?User $user = null;

    #[Assert\Expression(
        "this.getDateFrom() <= this.getDateTo()",
        message: "Data od nie może być późniejsza niż data do"
    )]
    public function isValidDateRange(): bool { return true; }

    // ===== Gettery i Settery =====
    public function getId(): ?int { return $this->id; }
    public function getCompany(): ?string { return $this->company; }
    public function setCompany(?string $company): self { $this->company = $company; return $this; }
    public function getPosition(): ?string { return $this->position; }
    public function setPosition(?string $position): self { $this->position = $position; return $this; }
    public function getDateFrom(): ?\DateTimeInterface { return $this->dateFrom; }
    public function setDateFrom(?\DateTimeInterface $dateFrom): self { $this->dateFrom = $dateFrom; return $this; }
    public function getDateTo(): ?\DateTimeInterface { return $this->dateTo; }
    public function setDateTo(?\DateTimeInterface $dateTo): self { $this->dateTo = $dateTo; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(User $user): self { $this->user = $user; return $this; }
}
