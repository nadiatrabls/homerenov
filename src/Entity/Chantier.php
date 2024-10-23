<?php



namespace App\Entity;

use App\Repository\ChantierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Abonne;

#[ORM\Entity(repositoryClass: ChantierRepository::class)]
class Chantier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]  // Utilisation de JSON pour stocker un tableau d'images
    private array $images = [];

    // Ajout de la relation Many-to-One avec Abonne
    #[ORM\ManyToOne(targetEntity: Abonne::class)]
    #[ORM\JoinColumn(nullable: true)] // Rends temporairement cette colonne nullable
    private ?Abonne $client = null;

    // Getters et setters pour la nouvelle relation
    public function getClient(): ?Abonne
    {
        return $this->client;
    }

    public function setClient(?Abonne $client): static
    {
        $this->client = $client;

        return $this;
    }

    // Les autres méthodes existantes
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function setImages(array $images): self
    {
        $this->images = $images;

        return $this;
    }
}

