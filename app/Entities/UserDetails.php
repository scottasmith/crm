<?php

declare(strict_types=1);

namespace App\Entities;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;
use Exception;

#[Entity]
#[Table(name: 'user_detail')]
class UserDetails
{
    #[Id]
    #[OneToOne(inversedBy: 'userDetails', targetEntity: User::class)]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $user;

    #[Column(type: 'string', nullable: false)]
    private ?string $title;

    #[Column(name: 'given_name', type: 'string', nullable: false)]
    private ?string $givenName;

    #[Column(type: 'string', nullable: false)]
    private ?string $surname;

    #[Column(type: 'string', nullable: true)]
    private ?string $position;

    #[Column(name: 'home_tel', type: 'string', nullable: true)]
    private ?string $homeTel;

    #[Column(name: 'mobile_tel', type: 'string', nullable: true)]
    private ?string $mobileTel;

    #[Column(name: 'work_tel', type: 'string', nullable: true)]
    private ?string $workTel;

    #[Column(name: 'other_tel', type: 'simple_array', nullable: true)]
    private ?array $otherTel;

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
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getGivenName(): string
    {
        return $this->givenName;
    }

    /**
     * @param string $givenName
     */
    public function setGivenName(string $givenName): void
    {
        $this->givenName = $givenName;
    }

    /**
     * @return string
     */
    public function getSurname(): string
    {
        return $this->surname;
    }

    /**
     * @param string $surname
     */
    public function setSurname(string $surname): void
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
     * @return array|null
     */
    public function getOtherTel(): ?array
    {
        return $this->otherTel;
    }

    /**
     * @param array|null $otherTel
     */
    public function setOtherTel(?array $otherTel): void
    {
        $this->otherTel = $otherTel;
    }

    /**
     * @param array $details
     * @return void
     * @throws Exception
     */
    public function fill(array $details): void
    {
        $requiredProperties = ['title', 'givenName', 'surname'];
        $missingProperties = [];

        foreach ($requiredProperties as $propertyIndex => $propertyName) {
            if (!isset($details[$propertyName])) {
                $missingProperties[] = $propertyName;
            }
        }

        if (count($missingProperties)) {
            throw new Exception('Required properties missing: ' . implode(',', $missingProperties));
        }

        foreach ($details as $property => $value) {
            $method = 'set' . ucfirst($property);

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }
}
