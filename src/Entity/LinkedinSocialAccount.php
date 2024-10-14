<?php

namespace App\Entity;

use App\Repository\LinkedinSocialAccountRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinkedinSocialAccountRepository::class)]
class LinkedinSocialAccount extends SocialAccount
{
}
