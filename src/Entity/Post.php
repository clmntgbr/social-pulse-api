<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post as CreatePost;
use App\ApiResource\Controller\CreatePostsAction;
use App\Entity\Traits\UuidTrait;
use App\Enum\PostGroupType;
use App\Enum\PostStatus;
use App\Repository\PostRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(
            normalizationContext: ['skip_null_values' => false, 'groups' => ['get_posts']],
        ),
        new CreatePost(
            uriTemplate: '/posts',
            controller: CreatePostsAction::class,
            paginationEnabled: false,
            denormalizationContext: ['skip_null_values' => false, 'groups' => ['post_posts']],
            read: false,
            name: 'post_posts'
        ),
    ]
)]
class Post
{
    use UuidTrait;
    use TimestampableEntity;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $postId = null;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['create_posts', 'get_posts'])]
    private ?string $groupUuid;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['create_posts', 'get_posts'])]
    private ?string $groupType;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['create_posts', 'get_posts'])]
    private ?string $header = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['create_posts', 'get_posts'])]
    private ?string $body = null;

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['create_posts', 'get_posts'])]
    private array $pictures;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['create_posts', 'get_posts'])]
    private string $status;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['create_posts', 'get_posts'])]
    private ?\DateTime $postAt = null;

    #[ORM\ManyToOne(targetEntity: SocialAccount::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['create_posts', 'get_posts'])]
    private ?SocialAccount $socialAccount = null;

    public function __construct()
    {
        $this->uuid = Uuid::uuid4()->toString();
        $this->groupUuid = Uuid::uuid4()->toString();
        $this->groupType = PostGroupType::SLAVE->toString();
        $this->pictures = [];
        $this->status = PostStatus::DRAFT->toString();
    }

    public function getPostId(): ?string
    {
        return $this->postId;
    }

    public function setPostId(?string $postId): static
    {
        $this->postId = $postId;

        return $this;
    }

    public function getGroupUuid(): ?string
    {
        return $this->groupUuid;
    }

    public function setGroupUuid(string $groupUuid): static
    {
        $this->groupUuid = $groupUuid;

        return $this;
    }

    public function getGroupType(): ?string
    {
        return $this->groupType;
    }

    public function setGroupType(string $groupType): static
    {
        $this->groupType = $groupType;

        return $this;
    }

    public function getHeader(): ?string
    {
        return $this->header;
    }

    public function setHeader(?string $header): static
    {
        $this->header = $header;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): static
    {
        $this->body = $body;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPostAt(): ?\DateTimeInterface
    {
        return $this->postAt;
    }

    public function setPostAt(?\DateTimeInterface $postAt): static
    {
        $this->postAt = $postAt;

        return $this;
    }

    public function getSocialAccount(): ?SocialAccount
    {
        return $this->socialAccount;
    }

    public function setSocialAccount(?SocialAccount $socialAccount): static
    {
        $this->socialAccount = $socialAccount;

        return $this;
    }
}
