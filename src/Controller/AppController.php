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
        private readonly PublicationServiceFactory $publicationServiceFactory
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
            ['threadUuid' => 'e2d3fec6-2d25-4ab9-a587-e41bb55ab17e'],
            ['id' => 'ASC']
        );

        $publicationService = $this->publicationServiceFactory->getService('twitter');
        $publicationService->publish($publications);

        dd($publications);
    }
}