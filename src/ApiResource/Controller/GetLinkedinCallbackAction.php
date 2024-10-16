<?php

namespace App\ApiResource\Controller;

use App\Dto\LinkedinCallback;
use App\Enum\SocialAccountStatus;
use App\Repository\LinkedinSocialAccountRepository;
use App\Repository\UserRepository;
use App\Service\LinkedinApi;
use App\Service\LinkedinLoginUrl;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[AsController]
class GetLinkedinCallbackAction extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly LinkedinSocialAccountRepository $linkedinSocialAccountRepository,
        private readonly LinkedinApi $linkedinApi,
        private readonly LinkedinLoginUrl $linkedinLoginUrl,

    ) {}

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function __invoke(LinkedinCallback $linkedinCallback): RedirectResponse
    {
        $user = $this->userRepository->findOneBy(['state' => $linkedinCallback->state]);

        if (!$user) {
            return new RedirectResponse('https://google.com?status=user_not_found');
        }

        $socialAccount = $this->linkedinSocialAccountRepository->findOneBy(['socialAccountId' => $linkedinCallback->state]);

        if (!$socialAccount) {
            return new RedirectResponse('https://google.com?status=social_account_not_found');
        }

        $this->linkedinSocialAccountRepository->delete($socialAccount);
        $accessToken = $this->linkedinApi->getAccessToken($linkedinCallback->code);
        $linkedinAccount = $this->linkedinApi->getUserProfile($accessToken);

        $this->linkedinSocialAccountRepository->updateOrCreate([
                'socialAccountId' => $linkedinAccount->sub,
                'workspace' => $user->getActiveWorkspace(),
            ], [
                'token' => $accessToken->access_token,
                'avatarUrl' => $linkedinAccount->picture ?? null,
                'socialAccountId' => $linkedinAccount->sub,
                'isVerified' => $linkedinAccount->email_verified ?? false,
                'workspace' => $user->getActiveWorkspace(),
                'username' => $linkedinAccount->name,
                'name' => sprintf('%s %s', $linkedinAccount->given_name, $linkedinAccount->family_name),
                'givenName' => $linkedinAccount->given_name,
                'familyName' => $linkedinAccount->family_name,
                'status' => SocialAccountStatus::ACTIF->toString(),
                'scopes' => $this->linkedinLoginUrl->getScopes(),
                'email' => $linkedinAccount->email,
            ]
        );

        return new RedirectResponse('https://google.com?status=ok');
    }
}