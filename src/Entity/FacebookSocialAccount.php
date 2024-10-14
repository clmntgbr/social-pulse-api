<?php

namespace App\Entity;

use App\Repository\FacebookSocialAccountRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FacebookSocialAccountRepository::class)]
class FacebookSocialAccount extends SocialAccount
{
}
