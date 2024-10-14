<?php

namespace App\Repository;

use App\Entity\SocialAccount;

interface InterfaceSocialAccountRepository
{
    public function create(array $data): SocialAccount;
    public function update(SocialAccount $entity, array $data): SocialAccount;
    public function delete(SocialAccount $entity): void;
    public function updateOrCreate(array $searchPayload, array $updatePayload): SocialAccount;
}
