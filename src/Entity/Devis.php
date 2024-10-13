<?php

namespace App\Entity;

use App\Repository\DevisRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DevisRepository::class)]
class Devis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $numero = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(length: 255)]
    private ?string $montant = null;

    #[ORM\Column(length: 255)]
    private ?string $adressChantier = null;

    // Relation ManyToOne vers l'entité Abonne
    #[ORM\ManyToOne(targetEntity: Abonne::class, inversedBy: 'devis')]
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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getAdressChantier(): ?string
    {
        return $this->adressChantier;
    }

    public function setAdressChantier(string $adressChantier): static
    {
        $this->adressChantier = $adressChantier;

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
