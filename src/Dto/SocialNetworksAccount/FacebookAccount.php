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
        $this->accounts = array_map(function ($datas) {
            $account = new FacebookData();
            $account->accessToken = $datas['access_token'];
            $account->name = $datas['name'];
            $account->id = $datas['id'];
            $account->pageToken = $datas['page_token'];
            $account->website = $datas['website'] ?? null;
            $account->picture = $datas['picture']['data']['url'] ?? null;
            $account->link = $datas['link'];
            $account->followersCount = $datas['followers_count'];
            $account->fanCount = $datas['fan_count'];

            return $account;
        }, $datas ?? []);

        return $this;
    }
}
