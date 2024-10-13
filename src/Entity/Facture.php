<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $numero = null;

    #[ORM\Column(type: 'date')]
    private ?DateTimeInterface $dateEmission = null;

    #[ORM\Column]
    private ?float $montant = null;

    #[ORM\Column(length: 255)]
    private ?string $refChantier = null;

    #[ORM\Column(length: 255)]
    private ?string $statusPaiement = null;

    #[ORM\ManyToOne(targetEntity: Abonne::class, inversedBy: 'factures')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
private ?string $fichier = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getDateEmission(): ?DateTimeInterface
    {
        return $this->dateEmission;
    }

    public function setDateEmission(DateTimeInterface $dateEmission): static
    {
        $this->dateEmission = $dateEmission;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getRefChantier(): ?string
    {
        return $this->refChantier;
    }

    public function setRefChantier(string $refChantier): static
    {
        $this->refChantier = $refChantier;

        return $this;
    }

    public function getStatusPaiement(): ?string
    {
        return $this->statusPaiement;
    }

    public function setStatusPaiement(string $statusPaiement): static
    {
        $this->statusPaiement = $statusPaiement;

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
    public function getFichier(): ?string
{
    return $this->fichier;
}

public function setFichier(?string $fichier): self
{
    $this->fichier = $fichier;

    return $this;
}
}
