<?php

namespace App\Entity\SocialNetwork;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Traits\UuidTrait;
use App\Repository\SocialNetwork\TypeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: TypeRepository::class)]
#[ApiResource(
    operations: [],
    order: ['createdAt' => 'DESC', 'name' => 'ASC'],
)]
class Type
{
    use UuidTrait;
    use TimestampableEntity;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['social-networks-type:get'])]
    private ?string $name;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['social-networks-type:get'])]
    private ?string $color;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }
}
