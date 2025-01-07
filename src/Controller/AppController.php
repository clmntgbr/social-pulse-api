<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class AppController extends AbstractController
{
    #[Route('/api/status', name: 'status', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return new JsonResponse(['status' => 'OK']);
    }

    #[Route('/debug', name: 'debug', methods: ['GET'])]
    public function debug()
    {
        phpinfo();
    }
}