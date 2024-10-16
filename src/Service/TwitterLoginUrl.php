<?php

namespace App\Service;

use Abraham\TwitterOAuth\TwitterOAuth;
use Abraham\TwitterOAuth\TwitterOAuthException;
use App\Dto\TwitterOAuthToken;
use App\Entity\User;
use App\Repository\TwitterSocialAccountRepository;
use App\Repository\UserRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TwitterLoginUrl implements InterfaceLoginUrl
{
    public function __construct(
        private readonly string                         $twitterCallbackUrl,
        private readonly string                         $twitterApiUrl,
        private readonly string                         $twitterApiKey,
        private readonly string                         $twitterApiSecret,
        private readonly UserRepository                 $userRepository,
        private readonly TwitterSocialAccountRepository $twitterSocialAccountRepository,
        private readonly SerializerInterface            $serializer,
        private readonly ValidatorInterface             $validator,
        private readonly ValidatorError                 $validatorError,
        private ?TwitterOAuth                           $twitterOAuth = null
    ) {
        $this->twitterOAuth = new TwitterOAuth($this->twitterApiKey, $this->twitterApiSecret);
    }

    /**
     * @throws TwitterOAuthException
     */
    public function getLoginUrl(User $user, string $callback): string
    {
        $user = $this->userRepository->update($user, [
            'state' => Uuid::uuid4()->toString(),
            'callback' => $callback
        ]);

        $authLink = $this->twitterOAuth->oauth("oauth/request_token", ["oauth_callback" => sprintf('%s?state=%s', $this->twitterCallbackUrl, $user->getState())]);
        $twitterOauthToken = $this->validateOauthToken($authLink);

        $this->twitterSocialAccountRepository->create([
            'token' => $twitterOauthToken->oauth_token,
            'tokenSecret' => $twitterOauthToken->oauth_token_secret,
            'socialAccountId' => $user->getState(),
            'workspace' => $user->getActiveWorkspace(),
            'scopes' => $this->getScopes(),
        ]);

        return sprintf('%s/oauth/authenticate?oauth_token=%s', $this->twitterApiUrl, $twitterOauthToken->oauth_token);
    }

    private function validateOauthToken(array $data): TwitterOAuthToken
    {
        $oauthToken = $this->serializer->deserialize(json_encode($data), TwitterOAuthToken::class, 'json');
        $errors = $this->validator->validate($oauthToken);
        if (count($errors) > 0) {
            throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
        }
        return $oauthToken;
    }

    public function getScopes(): string
    {
        return 'created_at,description,entities,id,location,most_recent_tweet_id,name,pinned_tweet_id,profile_image_url,protected,public_metrics,url,username,verified,verified_type,withheld';
    }
}