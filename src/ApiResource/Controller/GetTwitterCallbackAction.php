<?php

namespace App\ApiResource\Controller;

use Abraham\TwitterOAuth\TwitterOAuthException;
use App\Dto\TwitterCallback;
use App\Enum\SocialAccountStatus;
use App\Repository\TwitterSocialAccountRepository;
use App\Repository\UserRepository;
use App\Service\TwitterApi;
use App\Service\TwitterLoginUrl;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[AsController]
class GetTwitterCallbackAction extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TwitterSocialAccountRepository $twitterSocialAccountRepository,
        private readonly TwitterApi $twitterApi,
        private readonly TwitterLoginUrl $twitterLoginUrl,
        private readonly string $frontUrl

    ) {}

    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws TwitterOAuthException
     */
    public function __invoke(TwitterCallback $twitterCallback): RedirectResponse
    {
        $user = $this->userRepository->findOneBy(['state' => $twitterCallback->state]);

        if (!$user) {
            return new RedirectResponse(sprintf('%s?status=pasOk', $this->frontUrl));
        }

        $socialAccount = $this->twitterSocialAccountRepository->findOneBy(['socialAccountId' => $twitterCallback->state]);

        if (!$socialAccount) {
            return new RedirectResponse(sprintf('%s?status=pasOk', $this->frontUrl));
        }

        $this->twitterSocialAccountRepository->delete($socialAccount);
        $accessToken = $this->twitterApi->getAccessToken($twitterCallback->oauth_token, $twitterCallback->oauth_verifier);
        $bearerToken = $this->twitterApi->getBearerToken();
        $twitterAccount = $this->twitterApi->getUserProfile($accessToken);

        $this->twitterSocialAccountRepository->updateOrCreate([
                'socialAccountId' => $twitterAccount->id,
                'workspace' => $user->getActiveWorkspace(),
            ], [
                'token' => $accessToken->oauth_token,
                'tokenSecret' => $accessToken->oauth_token_secret,
                'bearerToken' => $bearerToken->access_token,
                'avatarUrl' => $twitterAccount->profile_image_url ?? null,
                'socialAccountId' => $twitterAccount->id,
                'isVerified' => $twitterAccount->verified ?? false,
                'workspace' => $user->getActiveWorkspace(),
                'username' => $twitterAccount->username,
                'name' => $twitterAccount->name,
                'status' => SocialAccountStatus::ACTIF->toString(),
                'scopes' => $this->twitterLoginUrl->getScopes(),
            ]
        );

        return new RedirectResponse(sprintf('%s?status=ok', $this->frontUrl));
    }
}