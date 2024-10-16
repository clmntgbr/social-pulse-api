<?php

namespace App\ApiResource\Controller;

use App\Entity\User;
use App\Service\LinkedinLoginUrl;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class GetLinkedinLoginUrlAction extends AbstractController
{
    public function __construct(
        private readonly LinkedinLoginUrl $linkedinLoginUrl,
        private readonly SerializerInterface $serializer,
    ) {}

    public function __invoke(Request $request, #[CurrentUser] User $user): JsonResponse
    {
        $url = $this->linkedinLoginUrl->getLoginUrl($user, $request->query->get('callback', '/'));

        return new JsonResponse(
            data: $this->serializer->serialize(['value' => $url], 'jsonld'),
            status: Response::HTTP_OK,
            json: true
        );
    }
}