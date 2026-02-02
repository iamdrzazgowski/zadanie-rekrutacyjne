<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity]
class User
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"string", length:255)]
    #[Assert\NotBlank]
    private ?string $firstName = null;

    #[ORM\Column(type:"string", length:255)]
    #[Assert\NotBlank]
    private ?string $lastName = null;

    #[ORM\Column(type:"date")]
    #[Assert\NotBlank]
    #[Assert\LessThan("today", message:"Data urodzenia musi być w przeszłości")]
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\OneToOne(mappedBy:"user", cascade:["persist", "remove"])]
    private ?Contact $contact = null;

    #[ORM\OneToMany(mappedBy:"user", targetEntity:Experience::class, cascade:["persist"], orphanRemoval:true)]
    private $experiences;

    public function __construct()
    {
        $this->experiences = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getFirstName(): ?string { return $this->firstName; }
    public function setFirstName(?string $firstName): self { $this->firstName = $firstName; return $this; }
    public function getLastName(): ?string { return $this->lastName; }
    public function setLastName(?string $lastName): self { $this->lastName = $lastName; return $this; }
    public function getBirthDate(): ?\DateTimeInterface { return $this->birthDate; }
    public function setBirthDate(?\DateTimeInterface $birthDate): self { $this->birthDate = $birthDate; return $this; }
    public function getContact(): ?Contact { return $this->contact; }
    public function setContact(Contact $contact): self { $this->contact = $contact; return $this; }
    public function getExperiences() { return $this->experiences; }
    public function addExperience(Experience $exp): self { $this->experiences[] = $exp; return $this; }
}
