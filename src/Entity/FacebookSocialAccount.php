<?php

namespace App\Entity;

use App\Enum\SocialAccountType;
use App\Repository\FacebookSocialAccountRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FacebookSocialAccountRepository::class)]
class FacebookSocialAccount extends SocialAccount
{
    public function __construct()
    {
        parent::__construct();
        $this->setSocialAccountType(SocialAccountType::FACEBOOK->toString());
        $this->setSocialAccountTypeAvatarUrl('/images/facebook-logo.png');
    }
}
