<?php

namespace App\Dto\SocialNetworksAccount;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class FacebookAccount extends AbstractAccount
{
    #[SerializedName('accounts')]
    #[Assert\Valid]
    public array $accounts;

    #[Assert\Type('string')]
    #[Assert\Email()]
    public ?string $email;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public ?string $id;

    public function setAccounts(?array $accounts): self
    {
        $datas = $accounts['data'] ?? [];
        $this->accounts = array_map(function ($datas): FacebookData {
            $facebookData = new FacebookData();
            $facebookData->accessToken = $datas['access_token'];
            $facebookData->name = $datas['name'];
            $facebookData->id = $datas['id'];
            $facebookData->pageToken = $datas['page_token'];
            $facebookData->website = $datas['website'] ?? null;
            $facebookData->picture = $datas['picture']['data']['url'] ?? null;
            $facebookData->link = $datas['link'];
            $facebookData->followersCount = $datas['followers_count'];
            $facebookData->fanCount = $datas['fan_count'];

            return $facebookData;
        }, $datas ?? []);

        return $this;
    }
}
