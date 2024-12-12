<?php

namespace App\ApiResource;

use App\Dto\Api\PostSocialNetworksValidate;
use App\Entity\User;
use App\Enum\SocialNetworkStatus;
use App\Repository\SocialNetwork\SocialNetworkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[AsController]
class PostSocialNetworksValidateAction extends AbstractController
{
    public function __construct(
        private readonly SocialNetworkRepository $socialNetworkRepository
    ) {}

    public function __invoke(PostSocialNetworksValidate $postSocialNetworksValidate, #[CurrentUser] ?User $user): JsonResponse
    {
        $isSocialNetworkValid = $this->socialNetworkRepository->findOneBy([
            'validate' => $postSocialNetworksValidate->validate,
        ]);

        if (!$isSocialNetworkValid) {
            return new JsonResponse(
                data: [],
                status: Response::HTTP_OK
            );
        }

        if ($user->getActiveOrganization()->getUuid() !== $isSocialNetworkValid->getOrganization()->getUuid()) {
            return new JsonResponse(['message' => 'You dont have access to this social network(s).'],
                Response::HTTP_FORBIDDEN
            );
        }

        $socialNetworks = $this->socialNetworkRepository->findBy([
            'uuid' => $postSocialNetworksValidate->uuids,
        ]);

        foreach ($socialNetworks as $socialNetwork) {
            $this->socialNetworkRepository->update($socialNetwork, [
                'validate' => null,
                'status' => SocialNetworkStatus::ACTIVE->toString(),
            ]);
        }

        $socialNetworks = $this->socialNetworkRepository->findBy([
            'validate' => $postSocialNetworksValidate->validate,
            'status' => SocialNetworkStatus::TEMPORARY->toString(),
        ]);

        foreach ($socialNetworks as $socialNetwork) {
            $this->socialNetworkRepository->delete($socialNetwork);
        }

        return new JsonResponse(
            data: [],
            status: Response::HTTP_OK
        );
    }
}