<?php

namespace App\Service\SocialNetworks\Connect;

use Abraham\TwitterOAuth\TwitterOAuth;
use Abraham\TwitterOAuth\TwitterOAuthException;
use App\Dto\AccessToken\TwitterAccessToken;
use App\Dto\AccessToken\TwitterBearerToken;
use App\Dto\Api\GetSocialNetworksCallback;
use App\Dto\SocialNetworksAccount\TwitterAccount;
use App\Dto\TwitterOAuthToken;
use App\Entity\User;
use App\Enum\SocialNetworkType;
use App\Repository\SocialNetwork\TwitterSocialNetworkRepository;
use App\Repository\SocialNetwork\TypeRepository;
use App\Repository\UserRepository;
use App\Service\TwitterApi;
use App\Service\ValidatorError;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class TwitterSocialNetworkService implements SocialNetworkServiceInterface
{
    public function __construct(
        private readonly TwitterApi $twitterApi,
        private readonly UserRepository $userRepository,
        private readonly TwitterSocialNetworkRepository $twitterSocialNetworkRepository,
        private readonly TypeRepository $typeRepository,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly ValidatorError $validatorError,
        private readonly string $twitterApiUrl,
        private readonly string $twitterApiKey,
        private readonly string $twitterApiSecret,
        private readonly string $callbackUrl,
        private readonly string $frontUrl,
        private ?TwitterOAuth $twitterOAuth = null,
    ) {
        $this->twitterOAuth = new TwitterOAuth($this->twitterApiKey, $this->twitterApiSecret);
    }

    public function getConnectUrl(User $user, string $callbackPath): string
    {
        /** @var User $user */
        $user = $this->userRepository->update($user, [
            'socialNetworksState' => Uuid::uuid4()->toString(),
            'socialNetworksCallbackPath' => $callbackPath,
        ]);

        try {
            $authLink = $this->twitterOAuth->oauth('oauth/request_token', [
                'oauth_callback' => sprintf('%s?state=%s', sprintf($this->callbackUrl, 'twitter'), $user->getSocialNetworksState()),
            ]);

            /** @var TwitterOAuthToken $twitterOAuthToken */
            $twitterOAuthToken = $this->serializer->deserialize(json_encode($authLink), TwitterOAuthToken::class, 'json');

            $errors = $this->validator->validate($twitterOAuthToken);
            if (count($errors) > 0) {
                throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
            }

            return sprintf('%s/oauth/authenticate?oauth_token=%s',
                $this->twitterApiUrl,
                $twitterOAuthToken->oauthToken
            );
        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TwitterOAuthException
     */
    public function create(GetSocialNetworksCallback $getSocialNetworksCallback): RedirectResponse
    {
        /** @var ?User $user */
        $user = $this->userRepository->findOneByCriteria(['socialNetworksState' => $getSocialNetworksCallback->state]);

        if (!$user) {
            return new RedirectResponse(sprintf('%s/en', $this->frontUrl));
        }

        $accessToken = $this->twitterApi->getAccessToken($getSocialNetworksCallback->oauthToken, $getSocialNetworksCallback->oauthVerifier);

        if (!$accessToken instanceof TwitterAccessToken) {
            return new RedirectResponse(sprintf('%s/%s', $this->frontUrl, $user->getSocialNetworksCallbackPath()));
        }

        $bearerToken = $this->twitterApi->getBearerToken();

        if (!$bearerToken instanceof TwitterBearerToken) {
            return new RedirectResponse(sprintf('%s/%s', $this->frontUrl, $user->getSocialNetworksCallbackPath()));
        }

        try {
            $twitterAccount = $this->twitterApi->getAccounts($accessToken);
        } catch(\Exception $exception) {
            return new RedirectResponse(sprintf('%s/%s', $this->frontUrl, $user->getSocialNetworksCallbackPath()));
        }

        $socialNetworkType = $this->typeRepository->findOneByCriteria(['name' => SocialNetworkType::TWITTER->toString()]);
        $validate = Uuid::uuid4()->toString();

        $this->twitterSocialNetworkRepository->updateOrCreate([
            'socialNetworkId' => $twitterAccount->id,
            'organization' => $user->getActiveOrganization(),
        ], [
            'socialNetworkId' => $twitterAccount->id,
            'avatarUrl' => $twitterAccount->profileImageUrl,
            'username' => $twitterAccount->username,
            'name' => $twitterAccount->name,
            'organization' => $user->getActiveOrganization(),
            'token' => $accessToken->oauthToken,
            'tokenSecret' => $accessToken->oauthTokenSecret,
            'isVerified' => $twitterAccount->verified,
            'bearerToken' => $bearerToken->accessToken,
            'followers' => $twitterAccount->publicMetrics->followersCount,
            'followings' => $twitterAccount->publicMetrics->followingsCount,
            'likes' => $twitterAccount->publicMetrics->likesCount,
            'validate' => $validate,
            'socialNetworkType' => $socialNetworkType,
        ]);

        return new RedirectResponse(sprintf('%s/%s/social-networks/validate/%s',
            $this->frontUrl,
            $user->getSocialNetworksCallbackPath(),
            $validate
        ));
    }
}