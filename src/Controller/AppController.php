<?php

namespace App\Controller;

use App\Repository\Publication\PublicationRepository;
use App\Service\Publications\PublicationServiceFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class AppController extends AbstractController
{
    public function __construct(
        private readonly PublicationRepository $publicationRepository,
        private readonly PublicationServiceFactory $publicationServiceFactory,
    ) {
    }

    #[Route('/api/status', name: 'status', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return new JsonResponse(['status' => 'OK']);
    }

    #[Route('/debug', name: 'debug', methods: ['GET'])]
    public function debug(): void
    {
        $publications = $this->publicationRepository->findBy(
            ['threadUuid' => '885f195e-d9b5-4eee-94e1-146a321869d3'],
            ['id' => 'ASC']
        );

        $publicationService = $this->publicationServiceFactory->getService('linkedin');
        $publicationService->publish($publications);

        dd($publications);
    }
}
