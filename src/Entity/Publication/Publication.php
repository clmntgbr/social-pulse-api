<?php

namespace App\Entity\Publication;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\ApiResource\GetPublicationAction;
use App\ApiResource\PostPublicationsAction;
use App\Entity\SocialNetwork\SocialNetwork;
use App\Entity\Traits\UuidTrait;
use App\Enum\PublicationStatus;
use App\Enum\PublicationThreadType;
use App\Repository\Publication\PublicationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PublicationRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['skip_null_values' => false, 'groups' => ['publications:get', 'social-networks:get', 'social-networks-type:get', 'default']],
        ),
        new Get(
            uriTemplate: '/publications/{uuid}',
            controller: GetPublicationAction::class,
        ),
        new Post(
            uriTemplate: '/publications',
            controller: PostPublicationsAction::class,
        ),
    ],
    order: ['publishedAt' => 'ASC']
)]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    'linkedin_publication' => 'LinkedinPublication',
    'twitter_publication' => 'TwitterPublication',
    'facebook_publication' => 'FacebookPublication',
    'youtube_publication' => 'YoutubePublication',
    'instagram_publication' => 'InstagramPublication',
])]
class Publication
{
    use UuidTrait;
    use TimestampableEntity;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['publication:get'])]
    private ?string $publicationId = null;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['publications:get', 'publication:get'])]
    private ?string $threadUuid;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['publications:get', 'publication:get'])]
    private ?string $threadType;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['publications:get', 'publication:get'])]
    private ?string $publicationType;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['publications:get', 'publication:get'])]
    private ?string $content;

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['publication:get'])]
    private array $pictures = [];

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['publications:get', 'publication:get'])]
    private string $status;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['publications:get', 'publication:get'])]
    private ?\DateTime $publishedAt = null;

    #[ORM\ManyToOne(targetEntity: SocialNetwork::class, inversedBy: 'publications')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['publications:get', 'publication:get'])]
    private ?SocialNetwork $socialNetwork = null;

    public function __construct()
    {
        $this->status = PublicationStatus::DRAFT->toString();
        $this->threadType = PublicationThreadType::PRIMARY->toString();
        $this->threadUuid = Uuid::uuid4()->toString();
        $this->initializeUuid();
    }

    #[Groups(['default'])]
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    #[Groups(['default'])]
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function getPublicationType(): ?string
    {
        return $this->publicationType;
    }

    public function setPublicationType(string $publicationType): static
    {
        $this->publicationType = $publicationType;

        return $this;
    }

    public function getSocialNetwork(): ?SocialNetwork
    {
        return $this->socialNetwork;
    }

    public function setSocialNetwork(?SocialNetwork $socialNetwork): static
    {
        $this->socialNetwork = $socialNetwork;

        return $this;
    }

    public function getPublicationId(): ?string
    {
        return $this->publicationId;
    }

    public function setPublicationId(?string $publicationId): static
    {
        $this->publicationId = $publicationId;

        return $this;
    }

    public function getThreadUuid(): ?string
    {
        return $this->threadUuid;
    }

    public function setThreadUuid(string $threadUuid): static
    {
        $this->threadUuid = $threadUuid;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeInterface $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getThreadType(): ?string
    {
        return $this->threadType;
    }

    public function setThreadType(string $threadType): static
    {
        $this->threadType = $threadType;

        return $this;
    }

    public function getPictures(): array
    {
        return $this->pictures;
    }

    public function setPictures(array $pictures): static
    {
        $this->pictures = $pictures;

        return $this;
    }
}
