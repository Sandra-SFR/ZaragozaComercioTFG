<?php

namespace App\Entity;

use App\Repository\CategoriaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriaRepository::class)]
class Categoria
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $icono = null;

    #[ORM\ManyToMany(targetEntity: Comercio::class, mappedBy: 'categorias')]
    private Collection $comercios;

    public function __construct()
    {
        $this->comercios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getIcono(): ?string
    {
        return $this->icono;
    }

    public function setIcono(string $icono): static
    {
        $this->icono = $icono;

        return $this;
    }

    /**
     * @return Collection<int, Comercio>
     */
    public function getComercios(): Collection
    {
        return $this->comercios;
    }

    public function addComercio(Comercio $comercio): static
    {
        if (!$this->comercios->contains($comercio)) {
            $this->comercios->add($comercio);
            $comercio->addCategoria($this);
        }

        return $this;
    }

    public function removeComercio(Comercio $comercio): static
    {
        if ($this->comercios->removeElement($comercio)) {
            $comercio->removeCategoria($this);
        }

        return $this;
    }
}
