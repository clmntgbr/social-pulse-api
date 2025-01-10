<?php

namespace App\Service;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidatorError
{
    public function getMessage(ConstraintViolationListInterface $constraintViolationList): array
    {
        return $this->hydrate($constraintViolationList);
    }

    public function getMessageToString(ConstraintViolationListInterface $constraintViolationList): string
    {
        return implode(', ', $this->hydrate($constraintViolationList));
    }

    private function hydrate(ConstraintViolationListInterface $constraintViolationList): array
    {
        $errorMessages = [];
        foreach ($constraintViolationList as $error) {
            $errorMessages[] = ucfirst($error->getPropertyPath()).': '.$error->getMessage();
        }

        return $errorMessages;
    }
}
