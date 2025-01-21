<?php

namespace App\Service\Publications;

use App\Dto\Api\PostPublications;
use App\Dto\Linkedin\LinkedinPost;
use App\Entity\Publication\LinkedinPublication;
use App\Entity\SocialNetwork\LinkedinSocialNetwork;
use App\Enum\PublicationStatus;
use App\Message\PublishScheduledPublicationsMessage;
use App\Repository\Publication\LinkedinPublicationRepository;
use App\Repository\SocialNetwork\LinkedinSocialNetworkRepository;
use App\Service\ImageService;
use App\Service\LinkedinApi;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class LinkedinPublicationService extends AbstractPublicationService implements PublicationServiceInterface
{
    public function __construct(
        private readonly LinkedinPublicationRepository $linkedinPublicationRepository,
        private readonly LinkedinSocialNetworkRepository $linkedinSocialNetworkRepository,
        private readonly PublicationService $publicationService,
        private readonly LinkedinApi $linkedinApi,
        private readonly ImageService $imageService,
        private readonly MessageBusInterface $messageBus,
        private readonly string $projectRoot,
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
            $mediaIds = [];
            foreach ($publication->getPictures() as $picture) {
                $media = $this->imageService->downloadTmp($picture);
                
                try {
                    $linkedinMedia = $this->linkedinApi->uploadMedia($publication->getSocialNetwork(), $media);
                    $this->imageService->delete($media);
                    $mediaIds[] = ['id' => $linkedinMedia->image, 'altText' => Uuid::uuid4()->toString()];
                } catch (\Exception $exception) {
                    dd($exception->getMessage());
                    $this->processPublicationError($publications, $publication->getThreadUuid(), $publication->getSocialNetwork()->getSocialNetworkType()->getName(), $exception->getMessage(), PublicationStatus::RETRY->toString());
                    return;
                }
            }
            try {
                /** @var LinkedinPost $response */
                $response = $this->linkedinApi->post($publication->getSocialNetwork(), [
                    'content' => $publication->getContent(),
                    'media' => $mediaIds,
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

    public function processPublicationError(array $publications, string $threadUuid, string $threadType, ?string $message, string $status): void
    {
        /** @var Publication $publication */
        foreach ($publications as $publication) {
            $this->linkedinPublicationRepository->update($publication, [
                'status' => $status,
                'statusMessage' => $message,
                'retry' => $publication->getRetry() + 1,
                'retryTime' => 3600,
            ]);
        }

        if ($status === PublicationStatus::RETRY->toString()) {
            $this->messageBus->dispatch(new PublishScheduledPublicationsMessage($threadUuid, $threadType), [
                new AmqpStamp('high', 0, []),
                new DelayStamp(3600000),
            ]);
        }
    }
}
