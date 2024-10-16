<?php

namespace App\ApiResource\Controller;

use App\Dto\FacebookCallback;
use App\Enum\SocialAccountStatus;
use App\Repository\FacebookSocialAccountRepository;
use App\Repository\UserRepository;
use App\Service\FacebookApi;
use App\Service\FacebookLoginUrl;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[AsController]
class GetFacebookCallbackAction extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly FacebookSocialAccountRepository $facebookSocialAccountRepository,
        private readonly FacebookApi $facebookApi,
        private readonly FacebookLoginUrl $facebookLoginUrl,

    ) {}

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function __invoke(FacebookCallback $facebookCallback): RedirectResponse
    {
        $user = $this->userRepository->findOneBy(['state' => $facebookCallback->state]);

        if (!$user) {
            return new RedirectResponse('https://google.com?status=pasOk');
        }

        $socialAccount = $this->facebookSocialAccountRepository->findOneBy(['socialAccountId' => $facebookCallback->state]);

        if (!$socialAccount) {
            return new RedirectResponse('https://google.com?status=pasOk');
        }

        $this->facebookSocialAccountRepository->delete($socialAccount);
        $accessToken = $this->facebookApi->getAccessToken($facebookCallback->code);
        $facebookAccounts = $this->facebookApi->getAccounts($accessToken);

        foreach ($facebookAccounts->accounts as $account) {
            $longAccessToken = $this->facebookApi->getLongAccessToken($account->access_token);
            $this->facebookSocialAccountRepository->updateOrCreate([
                    'socialAccountId' => $account->id,
                    'workspace' => $user->getActiveWorkspace(),
                ], [
                    'token' => $longAccessToken->access_token,
                    'avatarUrl' => $account->picture->data->url ?? null,
                    'socialAccountId' => $account->id,
                    'isVerified' => false,
                    'workspace' => $user->getActiveWorkspace(),
                    'username' => $account->name,
                    'name' => $account->name,
                    'status' => SocialAccountStatus::ACTIF->toString(),
                    'scopes' => $this->facebookLoginUrl->getScopes(),
                    'email' => $facebookAccounts->email,
                    'website' => $account->website,
                    'link' => $account->link,
                ]
            );
        }

        return new RedirectResponse('https://google.com?status=ok');
    }
}