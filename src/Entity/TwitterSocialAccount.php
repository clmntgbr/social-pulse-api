<?php

namespace App\Entity;

use App\Repository\TwitterSocialAccountRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TwitterSocialAccountRepository::class)]
class TwitterSocialAccount extends SocialAccount
{
}
