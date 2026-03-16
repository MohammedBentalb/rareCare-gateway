<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`users`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[OA\Property(type: 'string', format: 'uuid')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 180)]
    #[OA\Property(description: 'Email address')]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[OA\Property(description: 'Full name')]
    private ?string $name = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users')]
    private Collection $roles;

    /**
     * @var string The hashed password
     */

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Ignore]
    private ?string $password = null;

    public function __construct() {
        $this->roles = new ArrayCollection();
    }

    public function getId(): ?Uuid {
        return $this->id;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(string $email): static {
        $this->email = $email;
        return $this;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): static {
        $this->name = $name;
        return $this;
    }

 
    public function getUserIdentifier(): string {
        return (string) $this->email;
    }

    public function getRoles(): array {
        $roles = $this->roles->map(fn(Role $role) => $role->getName())->toArray();
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function addRole(Role $role): static {
        if (!$this->roles->contains($role)) $this->roles->add($role);
        return $this;
    }

    public function removeRole(Role $role): static {
        $this->roles->removeElement($role);
        return $this;
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    public function setPassword(string $password): static {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void {}
}
