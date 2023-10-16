<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $nombre = null;

    #[ORM\Column]
    private ?bool $envio = null;

    #[ORM\OneToMany(mappedBy: 'usuario', targetEntity: Comercio::class, orphanRemoval: true)]
    private Collection $comercios;

    public function __construct()
    {
        $this->comercios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function isEnvio(): ?bool
    {
        return $this->envio;
    }

    public function setEnvio(bool $envio): static
    {
        $this->envio = $envio;

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
            $comercio->setUsuario($this);
        }

        return $this;
    }

    public function removeComercio(Comercio $comercio): static
    {
        if ($this->comercios->removeElement($comercio)) {
            // set the owning side to null (unless already changed)
            if ($comercio->getUsuario() === $this) {
                $comercio->setUsuario(null);
            }
        }

        return $this;
    }
}
