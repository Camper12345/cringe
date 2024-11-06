<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface
{
    public const ROLE_ADMIN = 'admin';

    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255, nullable: false, unique: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $token = null;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $is_admin = false;

    /**
     * @var Collection<int, PageVisit>
     */
    #[ORM\OneToMany(targetEntity: PageVisit::class, mappedBy: 'user')]
    private Collection $pageVisits;

    public function __construct()
    {
        $this->pageVisits = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return Collection<int, PageVisit>
     */
    public function getPageVisits(): Collection
    {
        return $this->pageVisits;
    }

    public function addPageVisit(PageVisit $pageVisit): static
    {
        if (!$this->pageVisits->contains($pageVisit)) {
            $this->pageVisits->add($pageVisit);
            $pageVisit->setUser($this);
        }

        return $this;
    }

    public function removePageVisit(PageVisit $pageVisit): static
    {
        if ($this->pageVisits->removeElement($pageVisit)) {
            // set the owning side to null (unless already changed)
            if ($pageVisit->getUser() === $this) {
                $pageVisit->setUser(null);
            }
        }

        return $this;
    }

    public function isAdmin(): ?bool
    {
        return $this->is_admin;
    }

    public function setAdmin(bool $is_admin): static
    {
        $this->is_admin = $is_admin;

        return $this;
    }

    public function getRoles(): array {
        $roles = [];

        if($this->is_admin) {
            $roles[] = self::ROLE_ADMIN;
        }

        return $roles;
    }

    public function eraseCredentials(): void {
        return;
    }

    public function getUserIdentifier(): string {
        return (string) $this->getId();
    }

    public function toApi(): array {
        return [
            'name' => $this->getName(),
            'is_admin' => $this->is_admin,
        ];
    }
}
