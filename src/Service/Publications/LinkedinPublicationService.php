<?php

namespace App\Service\Publications;

use App\Dto\Api\PostPublications;
use App\Dto\Linkedin\LinkedinPost;
use App\Entity\Publication\LinkedinPublication;
use App\Entity\SocialNetwork\LinkedinSocialNetwork;
use App\Enum\PublicationStatus;
use App\Repository\Publication\LinkedinPublicationRepository;
use App\Repository\SocialNetwork\LinkedinSocialNetworkRepository;
use App\Service\LinkedinApi;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\Exception\ExceptionInterface;

class LinkedinPublicationService extends AbstractPublicationService implements PublicationServiceInterface
{
    public function __construct(
        private readonly LinkedinPublicationRepository $linkedinPublicationRepository,
        private readonly LinkedinSocialNetworkRepository $linkedinSocialNetworkRepository,
        private readonly PublicationService $publicationService,
        private readonly LinkedinApi $linkedinApi
    ) {
    }

    /**
     * @throws \Exception
     * @throws ExceptionInterface
     */
    public function create(PostPublications $postPublications): void
    {
        $socialNetwork = $this->linkedinSocialNetworkRepository->findOneByCriteria(['uuid' => $postPublications->socialNetworkUuid]);

        if (!$socialNetwork instanceof LinkedinSocialNetwork) {
            throw new \Exception('This social network does not exist');
        }

        $this->publicationService->save($postPublications, $socialNetwork, $this->linkedinPublicationRepository);
    }

    public function publish(array $publications)
    {
        /** @var LinkedinPublication $publication */
        foreach ($publications as $publication) {
            try {
                /** @var LinkedinPost $response */
                $response = $this->linkedinApi->post($publication->getSocialNetwork(), [
                    'content' => $publication->getContent(),
                ]);
            } catch (\Exception $exception) {
                $this->processPublicationError($publications, $publication->getThreadUuid(), $publication->getSocialNetwork()->getSocialNetworkType()->getName(), $exception->getMessage(), PublicationStatus::RETRY->toString());
                return;
            }

            $this->linkedinPublicationRepository->update($publication, [
                'publicationId' => $response->id,
                'status' => PublicationStatus::POSTED->toString(),
                'statusMessage' => null,
                'publishedAt' => new \DateTime(),
            ]);
        }
    }

    public function delete(array $publications)
    {
        /** @var LinkedinPublication $publication */
        foreach ($publications as $publication) {
            try {
                $this->linkedinApi->delete($publication);
                $this->linkedinPublicationRepository->delete($publication);
            } catch (\Exception $exception) {
                throw new BadRequestHttpException($exception->getMessage());
            }
        }
    }
}