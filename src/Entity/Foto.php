<?php

namespace App\Entity;

use App\Repository\FotoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FotoRepository::class)]
class Foto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $archivo = null;

    #[ORM\ManyToOne(inversedBy: 'fotos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Comercio $comercio = null;

    #[ORM\Column]
    private ?bool $destacada = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArchivo(): ?string
    {
        return $this->archivo;
    }

    public function setArchivo(string $archivo): static
    {
        $this->archivo = $archivo;

        return $this;
    }

    public function getComercio(): ?Comercio
    {
        return $this->comercio;
    }

    public function setComercio(?Comercio $comercio): static
    {
        $this->comercio = $comercio;

        return $this;
    }

    public function isDestacada(): ?bool
    {
        return $this->destacada;
    }

    public function setDestacada(bool $destacada): static
    {
        $this->destacada = $destacada;

        return $this;
    }
}
