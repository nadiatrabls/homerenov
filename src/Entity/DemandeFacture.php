<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Abonne;

#[ORM\Entity]
class DemandeFacture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $referenceChantier = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $factureAuNomDe = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $message = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    // Relation ManyToOne avec Abonne (utilisateur)
    #[ORM\ManyToOne(targetEntity: Abonne::class, inversedBy: 'demandesFacture')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Abonne $user = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // Getters et Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReferenceChantier(): ?string
    {
        return $this->referenceChantier;
    }

    public function setReferenceChantier(string $referenceChantier): self
    {
        $this->referenceChantier = $referenceChantier;
        return $this;
    }

    public function getFactureAuNomDe(): ?string
    {
        return $this->factureAuNomDe;
    }

    public function setFactureAuNomDe(string $factureAuNomDe): self
    {
        $this->factureAuNomDe = $factureAuNomDe;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUser(): ?Abonne
    {
        return $this->user;
    }

    public function setUser(?Abonne $user): self
    {
        $this->user = $user;
        return $this;
    }
}

