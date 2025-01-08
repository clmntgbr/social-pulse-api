<?php

namespace App\Service;

use App\Dto\AccessToken\FacebookAccessToken;
use App\Dto\SocialNetworksAccount\FacebookAccount;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class FacebookApi implements InterfaceApi
{
    public function __construct(
        private string $facebookClientId,
        private string $facebookClientSecret,
        private string $callbackUrl,
        private string $facebookApiUrl,
        private HttpClientInterface $client,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private ValidatorError $validatorError,
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getAccessToken(string ...$params): ?FacebookAccessToken
    {
        $url = sprintf('%s/oauth/access_token?client_id=%s&redirect_uri=%s&client_secret=%s&code=%s',
            $this->facebookApiUrl,
            $this->facebookClientId,
            sprintf($this->callbackUrl, 'facebook'),
            $this->facebookClientSecret,
            $params[0]
        );

        try {
            $response = $this->client->request('GET', $url);

            $facebookAccessToken = $this->serializer->deserialize($response->getContent(), FacebookAccessToken::class, 'json');

            $errors = $this->validator->validate($facebookAccessToken);
            if (count($errors) > 0) {
                throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
            }

            return $facebookAccessToken;
        } catch (ClientExceptionInterface $exception) {
            return null;
        }
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getLongAccessToken(string $token): ?FacebookAccessToken
    {
        $url = sprintf('%s/oauth/access_token?grant_type=fb_exchange_token&client_id=%s&redirect_uri=%s&client_secret=%s&fb_exchange_token=%s',
            $this->facebookApiUrl,
            $this->facebookClientId,
            sprintf($this->callbackUrl, 'facebook'),
            $this->facebookClientSecret,
            $token
        );

        try {
            $response = $this->client->request('GET', $url);

            $facebookAccessToken = $this->serializer->deserialize($response->getContent(), FacebookAccessToken::class, 'json');

            $errors = $this->validator->validate($facebookAccessToken);
            if (count($errors) > 0) {
                throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
            }

            return $facebookAccessToken;
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface|DecodingExceptionInterface
     */
    public function getAccounts(FacebookAccessToken $token): ?FacebookAccount
    {
        $url = sprintf('%s/me?fields=accounts{name,access_token,followers_count,fan_count,bio,emails,id,link,page_token,picture{url},website},email&access_token=%s',
            $this->facebookApiUrl,
            $token->accessToken
        );

        try {
            $response = $this->client->request('GET', $url);

            $facebookAccounts = $this->serializer->deserialize($response->getContent(), FacebookAccount::class, 'json');

            $errors = $this->validator->validate($facebookAccounts);
            if (count($errors) > 0) {
                throw new BadRequestHttpException($this->validatorError->getMessageToString($errors));
            }

            return $facebookAccounts;
        } catch (\Exception $exception) {
            return null;
        }
    }
}
