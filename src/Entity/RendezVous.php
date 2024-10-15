<?php

namespace App\Entity;

use App\Repository\RendezVousRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RendezVousRepository::class)]
class RendezVous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: 'time')]
    private ?\DateTimeInterface $heureDebut = null;

    #[ORM\Column(type: 'time')]
    private ?\DateTimeInterface $heureFin = null;

    #[ORM\Column(type: 'boolean')]
    private bool $disponible = true;

    #[ORM\ManyToOne(targetEntity: Abonne::class)]
    private ?Abonne $client = null;

   // Getters et setters

   public function getId(): ?int
   {
       return $this->id;
   }

   public function getDate(): ?\DateTimeInterface
   {
       return $this->date;
   }

   public function setDate(?\DateTimeInterface $date): self
   {
       $this->date = $date;

       return $this;
   }

   public function getHeureDebut(): ?\DateTimeInterface
   {
       return $this->heureDebut;
   }

   public function setHeureDebut(?\DateTimeInterface $heureDebut): self
   {
       $this->heureDebut = $heureDebut;

       return $this;
   }

   public function getHeureFin(): ?\DateTimeInterface
   {
       return $this->heureFin;
   }

   public function setHeureFin(?\DateTimeInterface $heureFin): self
   {
       $this->heureFin = $heureFin;

       return $this;
   }

   public function getDisponible(): bool
   {
       return $this->disponible;
   }

   public function setDisponible(bool $disponible): self
   {
       $this->disponible = $disponible;

       return $this;
   }

   public function getClient(): ?Abonne
   {
       return $this->client;
   }

   public function setClient(?Abonne $client): self
   {
       $this->client = $client;

       return $this;
   }
}