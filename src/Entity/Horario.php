<?php

namespace App\Entity;

use App\Repository\HorarioRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HorarioRepository::class)]
class Horario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $horaApertura = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $horaCierre = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $dia = null;

    #[ORM\ManyToOne(inversedBy: 'horarios')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Comercio $comercio = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHoraApertura(): ?\DateTimeInterface
    {
        return $this->horaApertura;
    }

    public function setHoraApertura(\DateTimeInterface $horaApertura): static
    {
        $this->horaApertura = $horaApertura;

        return $this;
    }

    public function getHoraCierre(): ?\DateTimeInterface
    {
        return $this->horaCierre;
    }

    public function setHoraCierre(\DateTimeInterface $horaCierre): static
    {
        $this->horaCierre = $horaCierre;

        return $this;
    }

    public function getDia(): ?int
    {
        return $this->dia;
    }

    public function setDia(int $dia): static
    {
        $this->dia = $dia;

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
}
