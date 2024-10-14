<?php

namespace App\Repository;

use App\Entity\SocialAccount;

interface InterfaceSocialAccountRepository
{
    public function create(array $data): SocialAccount;
}
