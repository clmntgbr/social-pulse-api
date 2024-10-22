<?php

namespace App\ApiResource\Controller;

use App\Dto\CreatePostsResponse;
use App\Entity\SocialAccount;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\SocialAccountRepository;
use DateTime;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class CreatePostsAction extends AbstractController
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly SocialAccountRepository $socialAccountRepository,
        private readonly SerializerInterface $serializer
    ) {}

    public function __invoke(CreatePostsResponse $createPostsResponse, #[CurrentUser] User $user): JsonResponse
    {
        $accounts = $user->getActiveWorkspace()->getSocialAccounts();
        $masterPost = $createPostsResponse->posts[0];

        $hasAccess = $accounts->exists(function ($key, SocialAccount $account) use ($masterPost) {
            return $account->getUuid() === $masterPost->socialAccountUuid;
        });

        if (!$hasAccess) {
            return new JsonResponse([
                'message' => 'You dont have access to this social account.',
            ],
                Response::HTTP_FORBIDDEN
            );
        }

        $socialAccount = $this->socialAccountRepository->findOneBy(['uuid' => $masterPost->socialAccountUuid]);

        if (!$socialAccount instanceof SocialAccount) {
            return new JsonResponse([
                'message' => 'This social account does not exist.',
            ],
                Response::HTTP_FORBIDDEN
            );
        }

        $groupUuid = Uuid::uuid4()->toString();

        foreach ($createPostsResponse->posts as $createPost) {
            $this->postRepository->create([
                'groupUuid' => $groupUuid,
                'groupType' => $createPost->groupType,
                'socialAccount' => $socialAccount,
                'body' => $createPost->body,
                'header' => $createPost->header,
                'postAt' => DateTime::createFromFormat('Y-m-d H:i:s', $createPost->postAt),
            ]);
        }

        $posts = $this->postRepository->findByGroupUuid($groupUuid);

        $context = (new ObjectNormalizerContextBuilder())
            ->withGroups('create_posts')
            ->toArray();

        return new JsonResponse(
            data: $this->serializer->serialize($posts, 'jsonld', $context),
            status: Response::HTTP_CREATED,
            json: true
        );
    }
}