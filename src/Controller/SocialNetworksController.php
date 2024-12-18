<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class SocialNetworksController extends AbstractController
{
    #[Route('/api/social_networks/confirm', name: 'social_networks_confirm', methods: ['POST'])]
    public function index(): JsonResponse
    {
        return new JsonResponse(['status' => 'OK']);
    }
}
