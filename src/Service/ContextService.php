<?php

namespace App\Service;

class ContextService
{
    public function getGroups(string $groups): ?array
    {
        if ($groups === '' || $groups === '0') {
            return null;
        }

        $groups = explode(',', $groups);

        if (count($groups) <= 0) {
            return null;
        }

        return $groups;
    }
}
