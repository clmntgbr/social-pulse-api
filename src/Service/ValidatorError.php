<?php

namespace App\Service;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidatorError
{
    public function getMessage(ConstraintViolationListInterface $violationList): array
    {
        return $this->hydrate($violationList);
    }

    public function getMessageToString(ConstraintViolationListInterface $violationList): string
    {
        return implode(', ', $this->hydrate($violationList));
    }

    private function hydrate(ConstraintViolationListInterface $violationList): array
    {
        $errorMessages = [];
        foreach ($violationList as $error) {
            $errorMessages[] = ucfirst($error->getPropertyPath()) . ': ' . $error->getMessage();
        }
        return $errorMessages;
    }
}