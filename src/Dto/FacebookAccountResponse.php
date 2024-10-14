<?php

namespace App\Dto;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints as Assert;

class FacebookAccountResponse
{
    #[Assert\Type('array')]
    #[Assert\Valid()]
    #[Assert\NotBlank()]
    /** @var FacebookAccount[] $accounts */
    public array $accounts;

    #[Assert\Type('string')]
    #[Assert\Email()]
    public ?string $email;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public ?string $id;

    public function __construct(
        ?string $email,
        ?string $id
    ) {
        $this->email = $email;
        $this->id = $id;
    }

    public static function hydrate(SerializerInterface $serializer, array $data): FacebookAccountResponse
    {
        $facebookResponse = new FacebookAccountResponse(
            $data['email'] ?? null,
            $data['id'] ?? null,
        );

        foreach ($data['accounts']['data'] ?? [] as $accountData) {
            $account = $serializer->deserialize(json_encode($accountData), FacebookAccount::class, 'json');
            $facebookResponse->accounts[] = $account;
        }

        return $facebookResponse;
    }
}
