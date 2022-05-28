<?php

declare(strict_types=1);

namespace App\Entities;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[Entity]
#[Table(name: 'role')]
class Role
{
    #[Id]
    #[Column(type: 'uuid', unique: true)]
    #[GeneratedValue(strategy: 'CUSTOM')]
    #[CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ManyToOne(targetEntity: Tenant::class, fetch: 'LAZY')]
    #[JoinColumn(name: 'tenant_id', referencedColumnName: 'id', nullable: true, onDelete: 'restrict')]
    private ?Tenant $tenant;

    #[Column(type: 'string')]
    private string $name;

    #[Column(type: 'string')]
    private string $description;

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

    public function __construct(?Tenant $tenant, string $name, string $description)
    {
        $this->id = Uuid::uuid4();
        $this->tenant = $tenant;
        $this->name = $name;
        $this->description = $description;
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
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
