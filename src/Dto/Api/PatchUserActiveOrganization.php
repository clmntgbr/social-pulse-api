<?php

namespace App\Dto\Api;

use Symfony\Component\Validator\Constraints as Assert;

class PatchUserActiveOrganization
{
    #[Assert\Type('string')]
    public ?string $organizationUuid;
}
