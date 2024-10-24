<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CreatePosts
{
    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    public ?string $groupType;

    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    public ?string $socialAccountUuid;

    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    public ?string $postAt;

    public ?string $body;
    public ?string $header;
    public array $pictures = [];

    public function __construct(
        ?string $groupType,
        ?string $header,
        ?string $body,
        ?string $socialAccountUuid,
        ?string $postAt,
        array $pictures
    ) {
        $this->groupType = $groupType;
        $this->header = $header;
        $this->body = $body;
        $this->postAt = $postAt;
        $this->pictures = $pictures;
        $this->socialAccountUuid = $socialAccountUuid;
    }

    #[Assert\Callback]
    public function validateContent(ExecutionContextInterface $context): void
    {
        if (empty($this->body) && empty($this->pictures)) {
            $context->buildViolation('Either body or pictures must be provided.')
                ->atPath('body')
                ->addViolation();
        }
    }
}
