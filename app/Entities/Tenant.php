<?php

declare(strict_types=1);

namespace App\Entities;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[Entity]
#[Table(name: 'tenant')]
#[Index(columns: ['name'], name: 'name_idx')]
#[Index(columns: ['slug'], name: 'slug_idx')]
#[Index(columns: ['name', 'is_active'], name: 'name_slug_idx')]
#[Index(columns: ['slug', 'is_active'], name: 'active_slug_idx')]
class Tenant
{
    #[Id, Column(type: 'uuid', unique: true)]
    #[GeneratedValue(strategy: 'CUSTOM'), CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[Column(type: 'string')]
    private ?string $name;

    #[Column(type: 'string', unique: true)]
    private ?string $slug;

    #[Column(type: 'string')]
    private ?string $description;

    #[Column(name: 'is_active', type: 'boolean')]
    private bool $isActive = false;

    #[OneToMany(mappedBy: 'tenant', targetEntity: TenantAuthProvider::class)]
    private Collection $authProviders;

    #[OneToMany(mappedBy: 'tenant', targetEntity: User::class)]
    private Collection $users;

    #[Column(name: 'deleted_datetime', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $deletedDateTime = null;

    #[Column(
        name: 'created_datetime',
        type: 'datetimetz_immutable',
        columnDefinition: 'datetime DEFAULT CURRENT_TIMESTAMP'
    )]
    private DateTimeImmutable $createdDateTime;

    #[Column(
        name: 'updated_datetime',
        type: 'datetimetz_immutable',
        columnDefinition: 'datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
    )]
    private DateTimeImmutable $updatedDateTime;

    /**
     * @param string $name
     * @param string $slug
     * @param string $description
     */
    public function __construct(string $name, string $slug, string $description)
    {
        $this->id = Uuid::uuid4();
        $this->name = $name;
        $this->slug = $slug;
        $this->description = $description;
        $this->authProviders = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->createdDateTime = new DateTimeImmutable();
        $this->updatedDateTime = new DateTimeImmutable();
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return Tenant
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     * @return Tenant
     */
    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getAuthProviders(): Collection
    {
        return $this->authProviders;
    }

    /**
     * @return Collection
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getDeletedDateTime(): ?DateTimeImmutable
    {
        return $this->deletedDateTime;
    }

    /**
     * @param DateTimeImmutable|null $deletedDateTime
     */
    public function setDeletedDateTime(?DateTimeImmutable $deletedDateTime): void
    {
        $this->deletedDateTime = $deletedDateTime;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedDateTime(): DateTimeImmutable
    {
        return $this->createdDateTime;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getUpdatedDateTime(): DateTimeImmutable
    {
        return $this->updatedDateTime;
    }
}
