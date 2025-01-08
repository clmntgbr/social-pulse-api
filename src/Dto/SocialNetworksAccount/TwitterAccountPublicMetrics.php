<?php

namespace App\Dto\SocialNetworksAccount;

use Symfony\Component\Serializer\Attribute\SerializedName;

class TwitterAccountPublicMetrics
{
    #[SerializedName('followers_count')]
    public int $followersCount = 0;

    #[SerializedName('following_count')]
    public int $followingsCount = 0;

    #[SerializedName('like_count')]
    public int $likesCount = 0;

    #[SerializedName('tweet_count')]
    public int $tweetsCount = 0;

    #[SerializedName('listed_count')]
    public int $listedsCount = 0;
}
