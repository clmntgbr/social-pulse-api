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
use App\ApiResource\Controller\GetLinkedinCallbackAction;
use App\ApiResource\Controller\GetLinkedinLoginUrlAction;
use App\ApiResource\Controller\GetTwitterCallbackAction;
use App\ApiResource\Controller\GetTwitterLoginUrlAction;
use App\Entity\Traits\UuidTrait;
use App\Enum\SocialAccountStatus;
use App\Repository\SocialAccountRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Attribute\Groups;

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
            uriTemplate: '/linkedin/login_url',
            controller: GetLinkedinLoginUrlAction::class,
            paginationEnabled: false,
            read: false,
            name: 'linkedin_login_url',
        ),
        new Get(
            uriTemplate: '/linkedin/callback',
            controller: GetLinkedinCallbackAction::class,
            paginationEnabled: false,
            read: false,
            name: 'linkedin_callback',
        ),
        new Get(
            uriTemplate: '/twitter/login_url',
            controller: GetTwitterLoginUrlAction::class,
            paginationEnabled: false,
            read: false,
            name: 'twitter_login_url',
        ),
        new Get(
            uriTemplate: '/twitter/callback',
            controller: GetTwitterCallbackAction::class,
            paginationEnabled: false,
            read: false,
            name: 'twitter_callback',
        ),
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
            normalizationContext: ['skip_null_values' => false, 'groups' => ['get_social_accounts']],
        )
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['type' => 'ipartial'])]
class SocialAccount
{
    use UuidTrait;
    use TimestampableEntity;

    #[ORM\Column(type: Types::STRING, unique: false)]
    #[Groups(['get_social_accounts', 'get_workspaces'])]
    private ?string $socialAccountId = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Groups(['get_social_accounts', 'get_workspaces'])]
    private ?bool $isVerified;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['get_social_accounts', 'get_workspaces'])]
    private ?string $username = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['get_social_accounts', 'get_workspaces'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['get_social_accounts', 'get_workspaces'])]
    private ?string $avatarUrl = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['get_social_accounts', 'get_workspaces'])]
    private ?string $socialAccountTypeAvatarUrl = null;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['get_social_accounts', 'get_workspaces'])]
    private string $status;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['get_social_accounts', 'get_workspaces'])]
    private string $socialAccountType;

    #[ORM\Column(type: Types::TEXT)]
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
    #[Groups(['get_social_accounts', 'get_workspaces'])]
    private ?string $email = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['get_social_accounts', 'get_workspaces'])]
    private ?string $givenName = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['get_social_accounts', 'get_workspaces'])]
    private ?string $familyName = null;

    #[ORM\ManyToOne(targetEntity: Workspace::class, inversedBy: 'socialAccounts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Workspace $workspace = null;

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

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(?string $avatarUrl): static
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }
    public function getGivenName(): ?string
    {
        return $this->givenName;
    }

    public function setGivenName(?string $givenName): static
    {
        $this->givenName = $givenName;

        return $this;
    }

    public function getFamilyName(): ?string
    {
        return $this->familyName;
    }

    public function setFamilyName(?string $familyName): static
    {
        $this->familyName = $familyName;

        return $this;
    }

    public function getSocialAccountType(): ?string
    {
        return $this->socialAccountType;
    }

    public function setSocialAccountType(string $socialAccountType): static
    {
        $this->socialAccountType = $socialAccountType;

        return $this;
    }

    public function getSocialAccountTypeAvatarUrl(): ?string
    {
        return $this->socialAccountTypeAvatarUrl;
    }

    public function setSocialAccountTypeAvatarUrl(string $socialAccountTypeAvatarUrl): static
    {
        $this->socialAccountTypeAvatarUrl = $socialAccountTypeAvatarUrl;

        return $this;
    }

    public function getWorkspace(): ?Workspace
    {
        return $this->workspace;
    }

    public function setWorkspace(?Workspace $workspace): static
    {
        $this->workspace = $workspace;

        return $this;
    }
}
