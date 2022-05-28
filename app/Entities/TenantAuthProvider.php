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
#[Table(name: 'tenant_auth_provider')]
class TenantAuthProvider
{
    #[Id]
    #[Column(type: 'uuid', unique: true)]
    #[GeneratedValue(strategy: 'CUSTOM')]
    #[CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ManyToOne(targetEntity: Tenant::class, fetch: 'LAZY')]
    #[JoinColumn(name: 'tenant_id', referencedColumnName: 'id', nullable: false, onDelete: 'restrict')]
    private Tenant $tenant;

    #[ManyToOne(targetEntity: TenantAuthProviderType::class, fetch: 'LAZY')]
    #[JoinColumn(name: 'provider_type_id', referencedColumnName: 'id', nullable: false, onDelete: 'restrict')]
    private TenantAuthProviderType $authProviderType;

    #[Column(name: 'options', type: 'json', nullable: true)]
    private array $options;

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
     * @param TenantAuthProviderType $authProviderType
     * @param array $options
     */
    public function __construct(Tenant $tenant, TenantAuthProviderType $authProviderType, array $options)
    {
        $this->id = Uuid::uuid4();
        $this->tenant = $tenant;
        $this->authProviderType = $authProviderType;
        $this->options = $options;
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
     * @return TenantAuthProviderType
     */
    public function getAuthProviderType(): TenantAuthProviderType
    {
        return $this->authProviderType;
    }

    /**
     * @return Tenant
     */
    public function getTenant(): Tenant
    {
        return $this->tenant;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
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
