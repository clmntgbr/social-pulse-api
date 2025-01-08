<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class JwtAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private string $publicJwt,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        $path = $request->getPathInfo();

        $excludedRoutes = [
            '/api/facebook/callback',
            '/api/linkedin/callback',
            '/api/twitter/callback',
        ];

        if (in_array($path, $excludedRoutes, true)) {
            return false;
        }

        return str_starts_with($path, '/api');
    }

    public function authenticate(Request $request): Passport
    {
        $token = $this->extractToken($request);

        if (null === $token) {
            throw new CustomUserMessageAuthenticationException('No token provided');
        }

        try {
            $payload = $this->decodeToken($token);
            $this->validateToken($payload);
            $payload = ['user_primary_email_address' => 'clement.goubier@gmail.com', 'user_id' => Uuid::uuid4()->toString()];
            $user = $this->getUser($payload);

            return new SelfValidatingPassport(
                new UserBadge($payload['user_primary_email_address'], function () use ($user) {
                    return $user;
                })
            );
        } catch (\Exception $e) {
            throw new CustomUserMessageAuthenticationException('Invalid token: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    private function getUser(array $payload): User
    {
        $user = $this->userRepository->updateOrCreate(
            [
                'email' => $payload['user_primary_email_address'],
            ],
            [
                'email' => $payload['user_primary_email_address'],
                'password' => Uuid::uuid4()->toString(),
                'avatarUrl' => $payload['user_image_url'] ?? null,
            ],
        );

        $user = $this->userRepository->update($user, [
            'password' => $this->userPasswordHasher->hashPassword($user, $payload['user_id']),
        ]);

        return $user;
    }

    private function validateToken(array $payload): void
    {
        if (!isset($payload['exp']) || $payload['exp'] < time()) {
            throw new CustomUserMessageAuthenticationException('Token has expired');
        }

        if (!isset($payload['iss']) || !str_ends_with($payload['iss'], '.clerk.accounts.dev')) {
            throw new CustomUserMessageAuthenticationException('Invalid token issuer');
        }

        if (!isset($payload['public_jwt']) || $payload['public_jwt'] !== $this->publicJwt) {
            throw new CustomUserMessageAuthenticationException('Invalid public token');
        }

        if (!isset($payload['sub'])) {
            throw new CustomUserMessageAuthenticationException('No subject claim found in token');
        }

        if (!isset($payload['user_primary_email_address'])) {
            throw new CustomUserMessageAuthenticationException('No email found in token');
        }
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => $exception->getMessage(),
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    private function extractToken(Request $request): ?string
    {
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        return substr($authHeader, 7);
    }

    private function decodeToken(string $token): array
    {
        $parts = explode('.', $token);

        if (3 !== count($parts)) {
            throw new CustomUserMessageAuthenticationException('Invalid token format');
        }

        $payload = json_decode($this->base64UrlDecode($parts[1]), true);

        if (!$payload) {
            throw new CustomUserMessageAuthenticationException('Invalid payload');
        }

        return $payload;
    }

    private function base64UrlDecode(string $input): string
    {
        $remainder = strlen($input) % 4;
        if ($remainder !== 0) {
            $padding = 4 - $remainder;
            $input .= str_repeat('=', $padding);
        }

        return base64_decode(strtr($input, '-_', '+/'));
    }
}
