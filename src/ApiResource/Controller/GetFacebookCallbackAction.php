<?php

namespace App\ApiResource\Controller;

use App\Dto\FacebookCallback;
use App\Enum\SocialAccountStatus;
use App\Repository\FacebookSocialAccountRepository;
use App\Repository\UserRepository;
use App\Service\FacebookApi;
use App\Service\FacebookLoginUrl;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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
        private EntityManagerInterface $em
    ) {}

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function __invoke(FacebookCallback $facebookCallback): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['state' => $facebookCallback->state]);

        if (!$user) {
            return new JsonResponse(
                data: 'api.basic.error',
                status: Response::HTTP_NOT_FOUND,
                json: true
            );
        }

        $socialAccount = $this->facebookSocialAccountRepository->findOneBy(['socialAccountId' => $facebookCallback->state]);

        if (!$socialAccount) {
            return new JsonResponse(
                data: 'api.basic.error',
                status: Response::HTTP_NOT_FOUND,
            );
        }

        $this->facebookSocialAccountRepository->delete($socialAccount);
        $accessToken = $this->facebookApi->getAccessToken($facebookCallback->code);
        $facebookAccounts = $this->facebookApi->getAccounts($accessToken);

        foreach ($facebookAccounts->accounts as $account) {
            $longAccessToken = $this->facebookApi->getLongAccessToken($account->access_token);
            $this->facebookSocialAccountRepository->updateOrCreate(
                [
                    'socialAccountId' => $account->id,
                ],
                [
                    'refreshUuid' => Uuid::uuid4()->toString(),
                    'token' => $longAccessToken->access_token,
                    'avatarUrl' => $account->picture->data->url ?? null,
                    'socialAccountId' => $account->id,
                    'isVerified' => false,
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

        return new JsonResponse(
            data: true,
            status: Response::HTTP_OK,
        );

    }
}