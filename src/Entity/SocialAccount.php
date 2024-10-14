<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\ApiResource\Controller\GetFacebookCallbackAction;
use App\ApiResource\Controller\GetFacebookLoginUrlAction;
use App\Entity\Traits\UuidTrait;
use App\Enum\SocialAccountStatus;
use App\Repository\SocialAccountRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Uuid;

#[ORM\Entity(repositoryClass: SocialAccountRepository::class)]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    'linkedin_social_account' => 'LinkedinSocialAccount',
    'twitter_social_account' => 'TwitterSocialAccount',
    'facebook_social_account' => 'FacebookSocialAccount',
])]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/facebook/login_url',
            controller: GetFacebookLoginUrlAction::class,
            paginationEnabled: false,
            read: false,
            name: 'facebook_login_url',
        ),
        new Get(
            uriTemplate: '/facebook/callback',
            controller: GetFacebookCallbackAction::class,
            paginationEnabled: false,
            read: false,
            name: 'facebook_callback',
        ),
        new Delete(),
        new Get(),
        new GetCollection(
            order: ['updatedAt' => 'ASC'],
            normalizationContext: ['skip_null_values' => false]
        )
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['type' => 'ipartial'])]
class SocialAccount
{
    use UuidTrait;
    use TimestampableEntity;

    #[ORM\Column(type: Types::GUID, length: 36)]
    private ?string $refreshUuid = null;

    #[ORM\Column(type: Types::STRING, unique: false)]
    private ?string $socialAccountId = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $isVerified;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING)]
    private string $status;

    #[ORM\Column(type: Types::STRING)]
    private ?string $token = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $bearerToken = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $refreshToken = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $tokenSecret = null;

    #[ORM\Column(type: Types::JSON)]
    private array $scopes;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $email = null;

    public function __construct()
    {
        $this->uuid = Uuid::uuid4()->toString();
        $this->isVerified = false;
        $this->status = SocialAccountStatus::TEMPORARY->toString();
        $this->scopes = [];
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(?string $uuid): static
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRefreshUuid(): ?string
    {
        return $this->refreshUuid;
    }

    public function setRefreshUuid(?string $refreshUuid): static
    {
        $this->refreshUuid = $refreshUuid;
        return $this;
    }

    public function getSocialAccountId(): ?string
    {
        return $this->socialAccountId;
    }

    public function setSocialAccountId(?string $socialAccountId): static
    {
        $this->socialAccountId = $socialAccountId;
        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(?bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): static
    {
        $this->token = $token;
        return $this;
    }

    public function getBearerToken(): ?string
    {
        return $this->bearerToken;
    }

    public function setBearerToken(?string $bearerToken): static
    {
        $this->bearerToken = $bearerToken;
        return $this;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(?string $refreshToken): static
    {
        $this->refreshToken = $refreshToken;
        return $this;
    }

    public function getTokenSecret(): ?string
    {
        return $this->tokenSecret;
    }

    public function setTokenSecret(?string $tokenSecret): static
    {
        $this->tokenSecret = $tokenSecret;
        return $this;
    }

    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function setScopes(string $scopes): static
    {
        $this->scopes = explode(',', $scopes);
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
