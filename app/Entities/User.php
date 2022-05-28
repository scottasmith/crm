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
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Illuminate\Contracts\Auth\Authenticatable;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[Entity]
#[Table(name: 'user')]
class User implements Authenticatable
{
    #[Id]
    #[Column(type: 'uuid', unique: true)]
    #[GeneratedValue(strategy: 'CUSTOM')]
    #[CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ManyToOne(targetEntity: Tenant::class, fetch: 'LAZY')]
    #[JoinColumn(name: 'tenant_id', referencedColumnName: 'id', unique: false, nullable: false, onDelete: 'restrict')]
    private ?Tenant $tenant;

    #[ManyToMany(targetEntity: Role::class, fetch: 'LAZY')]
    #[JoinTable(name: 'user_roles')]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'role_id', referencedColumnName: 'id')]
    private Collection $roles;

    #[Column(type: 'string', nullable: false)]
    private string $email;

    #[Column(name: 'remember_token', type: 'string', length: 100, nullable: true)]
    private ?string $rememberToken = null;

    #[Column(name: 'is_active', type: 'boolean')]
    private bool $isActive = true;

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
     * @param Tenant $tenant
     * @param string $email
     */
    public function __construct(Tenant $tenant, string $email)
    {
        $this->id = Uuid::uuid4();
        $this->tenant = $tenant;
        $this->roles = new ArrayCollection();
        $this->email = $email;
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
     * @return Tenant
     */
    public function getTenant(): Tenant
    {
        return $this->tenant;
    }

    /**
     * @return Collection
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @see Authenticatable
     * @return string
     */
    public function getRememberTokenName(): string
    {
        return 'rememberToken';
    }

    /**
     * @return string|null
     */
    public function getRememberToken(): ?string
    {
        return $this->rememberToken;
    }

    /**
     * @param string $value
     */
    public function setRememberToken($value): void
    {
        $this->rememberToken = $value;
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
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @see Authenticatable
     * @return string
     */
    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    /**
     * @see Authenticatable
     * @return string
     */
    public function getAuthIdentifier(): string
    {
        return $this->id->toString();
    }

    /**
     * @see Authenticatable
     * @return string
     */
    public function getAuthPassword(): string
    {
        // This will return empty because it will not be used by the custom auth provider
        return '';
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
