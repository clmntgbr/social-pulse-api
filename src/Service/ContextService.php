<?php

namespace App\Service;

class ContextService
{
    public function getGroups(string $groups): ?array
    {
        if ('' === $groups || '0' === $groups) {
            return null;
        }

        $groups = explode(',', $groups);

        if (count($groups) <= 0) {
            return null;
        }

        return $groups;
    }
}
