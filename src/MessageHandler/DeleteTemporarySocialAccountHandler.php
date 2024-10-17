<?php

namespace App\MessageHandler;

use App\Entity\SocialAccount;
use App\Message\DeleteTemporarySocialAccount;
use App\Repository\SocialAccountRepository;
use JetBrains\PhpStorm\NoReturn;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class DeleteTemporarySocialAccountHandler
{
    public function __construct(
        private LoggerInterface $logger,
        private SocialAccountRepository $socialAccountRepository
    ) {}

    #[NoReturn]
    public function __invoke(DeleteTemporarySocialAccount $message): void
    {
        $this->logger->debug(sprintf('Deleting temporary social account with uuid : %s', $message->getUuid()));

        $socialAccount = $this->socialAccountRepository->findOneBy(['uuid' => $message->getUuid()]);

        if(!$socialAccount instanceof SocialAccount) {
            return;
        }

        $this->socialAccountRepository->delete($socialAccount);
    }
}
