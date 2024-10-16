<?php

namespace App\Entity;

use App\Enum\SocialAccountType;
use App\Repository\LinkedinSocialAccountRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinkedinSocialAccountRepository::class)]
class LinkedinSocialAccount extends SocialAccount
{
    public function __construct()
    {
        parent::__construct();
        $this->setSocialAccountType(SocialAccountType::LINKEDIN->toString());
        $this->setSocialAccountTypeAvatarUrl('/images/linkedin-logo.png');
    }
}
