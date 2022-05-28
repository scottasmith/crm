<?php

declare(strict_types=1);

namespace App\Entities;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table(name: 'user_detail')]
class UserDetails
{
    #[Id]
    #[OneToOne(targetEntity: User::class)]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $user;

    #[Column(type: 'string', nullable: false)]
    private ?string $title;

    #[Column(name: 'given_name', type: 'string', nullable: false)]
    private ?string $givenName;

    #[Column(type: 'string', nullable: false)]
    private ?string $surname;

    #[Column(type: 'string', nullable: false)]
    private ?string $position;

    #[Column(name: 'home_tel', type: 'string', nullable: true)]
    private ?string $homeTel;

    #[Column(name: 'mobile_tel', type: 'string', nullable: true)]
    private ?string $mobileTel;

    #[Column(name: 'work_tel', type: 'string', nullable: true)]
    private ?string $workTel;

    #[Column(name: 'other_tel', type: 'simple_array', nullable: true)]
    private ?string $otherTel;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getGivenName(): ?string
    {
        return $this->givenName;
    }

    /**
     * @param string|null $givenName
     */
    public function setGivenName(?string $givenName): void
    {
        $this->givenName = $givenName;
    }

    /**
     * @return string|null
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    /**
     * @param string|null $surname
     */
    public function setSurname(?string $surname): void
    {
        $this->surname = $surname;
    }

    /**
     * @return string|null
     */
    public function getPosition(): ?string
    {
        return $this->position;
    }

    /**
     * @param string|null $position
     */
    public function setPosition(?string $position): void
    {
        $this->position = $position;
    }

    /**
     * @return string|null
     */
    public function getHomeTel(): ?string
    {
        return $this->homeTel;
    }

    /**
     * @param string|null $homeTel
     */
    public function setHomeTel(?string $homeTel): void
    {
        $this->homeTel = $homeTel;
    }

    /**
     * @return string|null
     */
    public function getMobileTel(): ?string
    {
        return $this->mobileTel;
    }

    /**
     * @param string|null $mobileTel
     */
    public function setMobileTel(?string $mobileTel): void
    {
        $this->mobileTel = $mobileTel;
    }

    /**
     * @return string|null
     */
    public function getWorkTel(): ?string
    {
        return $this->workTel;
    }

    /**
     * @param string|null $workTel
     */
    public function setWorkTel(?string $workTel): void
    {
        $this->workTel = $workTel;
    }

    /**
     * @return string|null
     */
    public function getOtherTel(): ?string
    {
        return $this->otherTel;
    }

    /**
     * @param string|null $otherTel
     */
    public function setOtherTel(?string $otherTel): void
    {
        $this->otherTel = $otherTel;
    }
}
